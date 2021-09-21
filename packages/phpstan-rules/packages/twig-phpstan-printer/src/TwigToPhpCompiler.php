<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TwigPHPStanPrinter;

use PhpParser\NodeTraverser;
use PhpParser\PrettyPrinter\Standard;
use Symplify\PHPStanRules\LattePHPStanPrinter\ValueObject\VariableAndType;
use Symplify\PHPStanRules\TwigPHPStanPrinter\PhpParser\NodeVisitor\TwigGetAttributeExpanderNodeVisitor;
use Symplify\PHPStanRules\TwigPHPStanPrinter\Twig\TolerantTwigEnvironment;
use Symplify\SmartFileSystem\SmartFileSystem;
use Twig\Loader\ArrayLoader;
use Twig\Node\ModuleNode;
use Twig\Source;

/**
 * @see \Symplify\PHPStanRules\TwigPHPStanPrinter\Tests\TwigToPhpCompiler\TwigToPhpCompilerTest
 */
final class TwigToPhpCompiler
{
    public function __construct(
        private SmartFileSystem $smartFileSystem,
        private \PhpParser\Parser $parser,
        private Standard $printerStandard,
        private TwigVarTypeDocBlockDecorator $twigVarTypeDocBlockDecorator,
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

        $nodeTraverser = new NodeTraverser();

        // replace twig_get_attribute with direct access/call
        $twigGetAttributeExpanderNodeVisitor = new TwigGetAttributeExpanderNodeVisitor();
        $nodeTraverser->addVisitor($twigGetAttributeExpanderNodeVisitor);
        $nodeTraverser->traverse($stmts);

        $phpContent = $this->printerStandard->prettyPrintFile($stmts);

        return $this->twigVarTypeDocBlockDecorator->decorateTwigContentWithTypes($phpContent, $variablesAndTypes);
    }
}
