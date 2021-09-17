<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\LattePHPStanPrinter;

use Latte\Parser;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\LattePHPStanPrinter\Latte\Filters\DefaultFilterMatcher;
use Symplify\PHPStanRules\LattePHPStanPrinter\Latte\LineCommentCorrector;
use Symplify\PHPStanRules\LattePHPStanPrinter\Latte\UnknownMacroAwareLatteCompiler;
use Symplify\PHPStanRules\LattePHPStanPrinter\PhpParser\NodeVisitor\MagicFilterToExplicitCallNodeVisitor;
use Symplify\PHPStanRules\LattePHPStanPrinter\ValueObject\VariableAndType;
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
        private VarTypeDocBlockDecorator $varTypeDocBlockDecorator,
    ) {
    }

    /**
     * @param array<VariableAndType> $variablesAndTypes
     */
    public function compileContent(string $templateFileContent, array $variablesAndTypes): string
    {
        $latteTokens = $this->latteParser->parse($templateFileContent);

        $rawPhpContent = $this->unknownMacroAwareLatteCompiler->compile($latteTokens, 'DummyTemplateClass');
        $rawPhpContent = $this->lineCommentCorrector->correctLineNumberPosition($rawPhpContent);

        $phpStmts = $this->parsePhpContentToPhpStmts($rawPhpContent);

        $this->transformFilterMagicClosureToExplicitStaticCall($phpStmts);
        $phpContent = $this->printerStandard->prettyPrintFile($phpStmts);

        return $this->varTypeDocBlockDecorator->decorateLatteContentWithTypes($phpContent, $variablesAndTypes);
    }

    /**
     * @param array<VariableAndType> $variablesAndTypes
     */
    public function compileFilePath(string $templateFilePath, array $variablesAndTypes): string
    {
        $templateFileContent = $this->smartFileSystem->readFile($templateFilePath);
        return $this->compileContent($templateFileContent, $variablesAndTypes);
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
     */
    private function transformFilterMagicClosureToExplicitStaticCall(array $phpStmts): void
    {
        $nodeTraverser = new NodeTraverser();
        $magicFilterToExplicitCallNodeVisitor = new MagicFilterToExplicitCallNodeVisitor(
            $this->simpleNameResolver,
            new DefaultFilterMatcher()
        );

        $nodeTraverser->addVisitor($magicFilterToExplicitCallNodeVisitor);
        $nodeTraverser->traverse($phpStmts);
    }
}
