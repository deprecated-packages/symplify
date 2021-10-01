<?php

declare(strict_types=1);

namespace Symplify\TwigPHPStanCompiler;

use Nette\Utils\Strings;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\SmartFileSystem\SmartFileSystem;
use Symplify\TemplatePHPStanCompiler\ValueObject\VariableAndType;
use Symplify\TwigPHPStanCompiler\Exception\TwigPHPStanCompilerException;
use Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor\CollectForeachedVariablesNodeVisitor;
use Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor\ExpandForeachContextNodeVisitor;
use Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor\RemoveUselessClassMethodsNodeVisitor;
use Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor\ReplaceEchoWithVarDocTypeNodeVisitor;
use Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor\TwigGetAttributeExpanderNodeVisitor;
use Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor\UnwrapCoalesceContextNodeVisitor;
use Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor\UnwrapContextVariableNodeVisitor;
use Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor\UnwrapTwigEnsureTraversableNodeVisitor;
use Symplify\TwigPHPStanCompiler\Reflection\PublicPropertyAnalyzer;
use Symplify\TwigPHPStanCompiler\Twig\TolerantTwigEnvironment;
use Twig\Lexer;
use Twig\Loader\ArrayLoader;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\Source;
use Twig\Token;
use Twig\TokenStream;

/**
 * @see \Symplify\TwigPHPStanCompiler\Tests\TwigToPhpCompiler\TwigToPhpCompilerTest
 */
final class TwigToPhpCompiler
{
    /**
     * @var string
     * @see https://regex101.com/r/dsL5Ou/1
     */
    public const TWIG_VAR_TYPE_DOCBLOCK_REGEX = '#\{\#\s+@var\s+(?<name>.*?)\s+(?<type>.*?)\s+\#}#';

    public function __construct(
        private SmartFileSystem $smartFileSystem,
        private Parser $parser,
        private Standard $printerStandard,
        private TwigVarTypeDocBlockDecorator $twigVarTypeDocBlockDecorator,
        private SimpleNameResolver $simpleNameResolver,
        private ObjectTypeMethodAnalyzer $objectTypeMethodAnalyzer,
        private PrivatesAccessor $privatesAccessor,
        private PublicPropertyAnalyzer $publicPropertyAnalyzer,
    ) {
    }

    /**
     * @param array<VariableAndType> $variablesAndTypes
     */
    public function compileContent(string $filePath, array $variablesAndTypes): string
    {
        $fileContent = $this->smartFileSystem->readFile($filePath);

        $tolerantTwigEnvironment = $this->createTwigEnvironment($filePath, $fileContent);

        $moduleNode = $this->parseFileContentToModuleNode($tolerantTwigEnvironment, $fileContent, $filePath);
        $rawPhpContent = $tolerantTwigEnvironment->compile($moduleNode);

        return $this->decoratePhpContent($rawPhpContent, $variablesAndTypes);
    }

    private function createTwigEnvironment(string $filePath, string $fileContent): TolerantTwigEnvironment
    {
        $arrayLoader = new ArrayLoader([
            $filePath => $fileContent,
        ]);

        return new TolerantTwigEnvironment($arrayLoader);
    }

    /**
     * @return ModuleNode<Node>
     */
    private function parseFileContentToModuleNode(
        TolerantTwigEnvironment $tolerantTwigEnvironment,
        string $fileContent,
        string $filePath
    ): ModuleNode {
        // this should disable comments as we know it - if we don't change it here, the tokenizer will remove all comments completely
        $lexer = new Lexer($tolerantTwigEnvironment, [
            'tag_comment' => ['{*', '*}'],
        ]);
        $tolerantTwigEnvironment->setLexer($lexer);

        $tokenStream = $tolerantTwigEnvironment->tokenize(new Source($fileContent, $filePath));

        $this->removeNonVarTypeDocCommentTokens($tokenStream);

        return $tolerantTwigEnvironment->parse($tokenStream);
    }

    /**
     * @param VariableAndType[] $variablesAndTypes
     */
    private function decoratePhpContent(string $phpContent, array $variablesAndTypes): string
    {
        $stmts = $this->parser->parse($phpContent);
        if ($stmts === null) {
            throw new TwigPHPStanCompilerException();
        }

        // -1. remove useless class methods
        $removeUselessClassMethodsNodeVisitor = new RemoveUselessClassMethodsNodeVisitor();
        $this->traverseStmtsWithVisitors($stmts, [$removeUselessClassMethodsNodeVisitor]);

        // 0. add types first?
        $this->unwarpMagicVariables($stmts);

        // 1. hacking {# @var variable type #} comments to /** @var types */
        $replaceEchoWithVarDocTypeNodeVisitor = new ReplaceEchoWithVarDocTypeNodeVisitor();
        $this->traverseStmtsWithVisitors($stmts, [$replaceEchoWithVarDocTypeNodeVisitor]);

        // get those types for further analysis
        $collectedVariablesAndTypes = $replaceEchoWithVarDocTypeNodeVisitor->getCollectedVariablesAndTypes();
        $variablesAndTypes = array_merge($variablesAndTypes, $collectedVariablesAndTypes);

        // 3. collect foreached variables to determine nested value :)
        $collectForeachedVariablesNodeVisitor = new CollectForeachedVariablesNodeVisitor($this->simpleNameResolver);
        $this->traverseStmtsWithVisitors($stmts, [$collectForeachedVariablesNodeVisitor]);

        // 2. replace twig_get_attribute with direct access/call
        $twigGetAttributeExpanderNodeVisitor = new TwigGetAttributeExpanderNodeVisitor(
            $this->simpleNameResolver,
            $this->objectTypeMethodAnalyzer,
            $this->publicPropertyAnalyzer,
            $variablesAndTypes,
            $collectForeachedVariablesNodeVisitor->getForeachedVariablesToSingles(),
        );

        $this->traverseStmtsWithVisitors($stmts, [$twigGetAttributeExpanderNodeVisitor]);

        $phpContent = $this->printerStandard->prettyPrintFile($stmts);
        return $this->twigVarTypeDocBlockDecorator->decorateTwigContentWithTypes($phpContent, $variablesAndTypes);
    }

    /**
     * @param Stmt[] $stmts
     * @param NodeVisitorAbstract[] $nodeVisitors
     */
    private function traverseStmtsWithVisitors(array $stmts, array $nodeVisitors): void
    {
        $nodeTraverser = new NodeTraverser();
        foreach ($nodeVisitors as $nodeVisitor) {
            $nodeTraverser->addVisitor($nodeVisitor);
        }

        $nodeTraverser->traverse($stmts);
    }

    /**
     * @param Stmt[] $stmts
     */
    private function unwarpMagicVariables(array $stmts): void
    {
        // 1. run $context unwrap first, as needed everywhere
        $unwrapContextVariableNodeVisitor = new UnwrapContextVariableNodeVisitor($this->simpleNameResolver);
        $this->traverseStmtsWithVisitors($stmts, [$unwrapContextVariableNodeVisitor]);

        // 2. unwrap coalesce $context
        $unwrapCoalesceContextNodeVisitor = new UnwrapCoalesceContextNodeVisitor($this->simpleNameResolver);
        $this->traverseStmtsWithVisitors($stmts, [$unwrapCoalesceContextNodeVisitor]);

        // 3. unwrap twig_ensure_traversable()
        $unwrapTwigEnsureTraversableNodeVisitor = new UnwrapTwigEnsureTraversableNodeVisitor(
            $this->simpleNameResolver
        );
        $this->traverseStmtsWithVisitors($stmts, [$unwrapTwigEnsureTraversableNodeVisitor]);

        // 4. expand foreached magic to make type references clear for iterated variables
        $expandForeachContextNodeVisitor = new ExpandForeachContextNodeVisitor($this->simpleNameResolver);
        $this->traverseStmtsWithVisitors($stmts, [$expandForeachContextNodeVisitor]);
    }

    private function removeNonVarTypeDocCommentTokens(TokenStream $tokenStream): void
    {
        /** @var Token[] $tokens */
        $tokens = $this->privatesAccessor->getPrivateProperty($tokenStream, 'tokens');

        foreach ($tokens as $key => $token) {
            if ($token->getType() !== Token::TEXT_TYPE) {
                continue;
            }

            // is comment text?
            if (! str_starts_with($token->getValue(), '{#')) {
                continue;
            }

            $match = Strings::match($token->getValue(), self::TWIG_VAR_TYPE_DOCBLOCK_REGEX);
            if ($match !== null) {
                continue;
            }

            unset($tokens[$key]);
        }

        $this->privatesAccessor->setPrivateProperty($tokenStream, 'tokens', $tokens);
    }
}
