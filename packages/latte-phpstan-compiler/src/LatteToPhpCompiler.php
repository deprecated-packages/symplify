<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler;

use Latte\Parser;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\LattePHPStanCompiler\Latte\Filters\FilterMatcher;
use Symplify\LattePHPStanCompiler\Latte\LineCommentCorrector;
use Symplify\LattePHPStanCompiler\Latte\UnknownMacroAwareLatteCompiler;
use Symplify\LattePHPStanCompiler\PhpParser\NodeVisitor\ControlRenderToExplicitCallNodeVisitor;
use Symplify\LattePHPStanCompiler\PhpParser\NodeVisitor\InstanceofRenderableNodeVisitor;
use Symplify\LattePHPStanCompiler\PhpParser\NodeVisitor\MagicFilterToExplicitCallNodeVisitor;
use Symplify\LattePHPStanCompiler\ValueObject\ComponentNameAndType;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;
use Symplify\SmartFileSystem\SmartFileSystem;
use Symplify\TemplatePHPStanCompiler\ValueObject\VariableAndType;

/**
 * @see \Symplify\LattePHPStanCompiler\Tests\LatteToPhpCompiler\LatteToPhpCompilerTest
 */
final class LatteToPhpCompiler
{
    public function __construct(
        private SmartFileSystem $smartFileSystem,
        private Parser $latteParser,
        private UnknownMacroAwareLatteCompiler $unknownMacroAwareLatteCompiler,
        private SimpleNameResolver $simpleNameResolver,
        private Standard $printerStandard,
        private LineCommentCorrector $lineCommentCorrector,
        private LatteVarTypeDocBlockDecorator $latteVarTypeDocBlockDecorator,
        private FilterMatcher $filterMatcher,
    ) {
    }

    /**
     * @param VariableAndType[] $variablesAndTypes
     * @param ComponentNameAndType[] $componentNamesAndtTypes
     */
    public function compileContent(
        string $templateFileContent,
        array $variablesAndTypes,
        array $componentNamesAndtTypes
    ): string {
        $this->ensureIsNotFilePath($templateFileContent);

        $latteTokens = $this->latteParser->parse($templateFileContent);

        $rawPhpContent = $this->unknownMacroAwareLatteCompiler->compile($latteTokens, 'DummyTemplateClass');
        $rawPhpContent = $this->lineCommentCorrector->correctLineNumberPosition($rawPhpContent);

        $phpStmts = $this->parsePhpContentToPhpStmts($rawPhpContent);

        $this->decorateStmts($phpStmts, $componentNamesAndtTypes);
        $phpContent = $this->printerStandard->prettyPrintFile($phpStmts);

        return $this->latteVarTypeDocBlockDecorator->decorateLatteContentWithTypes($phpContent, $variablesAndTypes);
    }

    /**
     * @param VariableAndType[] $variablesAndTypes
     * @param ComponentNameAndType[] $componentNamesAndTypes
     */
    public function compileFilePath(
        string $templateFilePath,
        array $variablesAndTypes,
        array $componentNamesAndTypes
    ): string {
        $templateFileContent = $this->smartFileSystem->readFile($templateFilePath);
        return $this->compileContent($templateFileContent, $variablesAndTypes, $componentNamesAndTypes);
    }

    /**
     * @return Stmt[]
     */
    private function parsePhpContentToPhpStmts(string $rawPhpContent): array
    {
        $parserFactory = new ParserFactory();

        $phpParser = $parserFactory->create(ParserFactory::PREFER_PHP7);
        return (array) $phpParser->parse($rawPhpContent);
    }

    /**
     * @param Stmt[] $phpStmts
     * @param ComponentNameAndType[] $componentNamesAndTypes
     */
    private function decorateStmts(array $phpStmts, array $componentNamesAndTypes): void
    {
        $nodeTraverser = new NodeTraverser();
        $magicFilterToExplicitCallNodeVisitor = new MagicFilterToExplicitCallNodeVisitor(
            $this->simpleNameResolver,
            $this->filterMatcher
        );

        $controlRenderToExplicitCallNodeVisitor = new ControlRenderToExplicitCallNodeVisitor(
            $this->simpleNameResolver,
            $componentNamesAndTypes
        );

        $instanceofRenderableNodeVisitor = new InstanceofRenderableNodeVisitor($this->simpleNameResolver);

        $nodeTraverser->addVisitor($magicFilterToExplicitCallNodeVisitor);
        $nodeTraverser->addVisitor($controlRenderToExplicitCallNodeVisitor);
        $nodeTraverser->addVisitor($instanceofRenderableNodeVisitor);

        $nodeTraverser->traverse($phpStmts);
    }

    private function ensureIsNotFilePath(string $templateFileContent): void
    {
        if (! file_exists($templateFileContent)) {
            return;
        }

        $errorMessage = sprintf(
            'The file path "%s" was passed as 1st argument in "%s()" metohd. Must be file content instead.',
            $templateFileContent,
            __METHOD__
        );
        throw new ShouldNotHappenException($errorMessage);
    }
}
