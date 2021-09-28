<?php

declare(strict_types=1);

namespace Symplify\TwigPHPStanCompiler;

use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\LattePHPStanCompiler\ValueObject\VariableAndType;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;
use Symplify\SmartFileSystem\SmartFileSystem;
use Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor\CollectForeachedVariablesNodeVisitor;
use Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor\ExpandForeachContextNodeVisitor;
use Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor\TwigGetAttributeExpanderNodeVisitor;
use Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor\UnwrapCoalesceContextNodeVisitor;
use Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor\UnwrapContextVariableNodeVisitor;
use Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor\UnwrapTwigEnsureTraversableNodeVisitor;
use Symplify\TwigPHPStanCompiler\Twig\TolerantTwigEnvironment;
use Twig\Loader\ArrayLoader;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\Source;

/**
 * @see \Symplify\TwigPHPStanCompiler\Tests\TwigToPhpCompiler\TwigToPhpCompilerTest
 */
final class TwigToPhpCompiler
{
    public function __construct(
        private SmartFileSystem $smartFileSystem,
        private Parser $parser,
        private Standard $printerStandard,
        private TwigVarTypeDocBlockDecorator $twigVarTypeDocBlockDecorator,
        private SimpleNameResolver $simpleNameResolver,
        private ObjectTypeMethodAnalyzer $objectTypeMethodAnalyzer,
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
        $phpContent = $tolerantTwigEnvironment->compile($moduleNode);

        return $this->decoratePhpContent($phpContent, $variablesAndTypes);
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
        $tokenStream = $tolerantTwigEnvironment->tokenize(new Source($fileContent, $filePath));

        return $tolerantTwigEnvironment->parse($tokenStream);
    }

    /**
     * @param VariableAndType[] $variablesAndTypes
     */
    private function decoratePhpContent(string $phpContent, array $variablesAndTypes): string
    {
        $stmts = $this->parser->parse($phpContent);
        if ($stmts === null) {
            throw new ShouldNotHappenException();
        }

        // 0. add types first?
        $this->unwarpMagicVariables($stmts);

        // 3. collect foreached variables to determine nested value :)
        $collectForeachedVariablesNodeVisitor = new CollectForeachedVariablesNodeVisitor($this->simpleNameResolver);
        $this->traverseStmtsWithVisitors($stmts, [$collectForeachedVariablesNodeVisitor]);

        // 2. replace twig_get_attribute with direct access/call
        $twigGetAttributeExpanderNodeVisitor = new TwigGetAttributeExpanderNodeVisitor(
            $this->simpleNameResolver,
            $this->objectTypeMethodAnalyzer,
            $variablesAndTypes,
            $collectForeachedVariablesNodeVisitor->getForeachedVariablesToSingles()
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
}
