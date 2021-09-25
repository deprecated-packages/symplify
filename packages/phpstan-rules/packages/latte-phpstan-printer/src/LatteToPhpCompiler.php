<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\LattePHPStanPrinter;

use Latte\Parser;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;
use Symplify\PHPStanRules\LattePHPStanPrinter\Latte\Filters\DefaultFilterMatcher;
use Symplify\PHPStanRules\LattePHPStanPrinter\Latte\LineCommentCorrector;
use Symplify\PHPStanRules\LattePHPStanPrinter\Latte\UnknownMacroAwareLatteCompiler;
use Symplify\PHPStanRules\LattePHPStanPrinter\PhpParser\NodeVisitor\ControlRenderToExplicitCallNodeVisitor;
use Symplify\PHPStanRules\LattePHPStanPrinter\PhpParser\NodeVisitor\MagicFilterToExplicitCallNodeVisitor;
use Symplify\PHPStanRules\LattePHPStanPrinter\ValueObject\VariableAndType;
use Symplify\PHPStanRules\ValueObject\ComponentNameAndType;
use Symplify\SmartFileSystem\SmartFileSystem;

/**
 * @see \Symplify\PHPStanRules\LattePHPStanPrinter\Tests\LatteToPhpCompiler\LatteToPhpCompilerTest
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
            new DefaultFilterMatcher()
        );

        $controlRenderToExplicitCallNodeVisitor = new ControlRenderToExplicitCallNodeVisitor(
            $this->simpleNameResolver,
            $componentNamesAndTypes
        );

        $nodeTraverser->addVisitor($magicFilterToExplicitCallNodeVisitor);
        $nodeTraverser->addVisitor($controlRenderToExplicitCallNodeVisitor);
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
