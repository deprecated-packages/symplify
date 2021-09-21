<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TwigPHPStanPrinter\Twig;

use Symplify\PHPStanRules\TwigPHPStanPrinter\TwigToPhpCompiler;
use Symplify\SmartFileSystem\SmartFileSystem;
use Twig\Loader\ArrayLoader;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\Source;

/**
 * @deprecated Use
 * @see TwigToPhpCompiler instead
 */
final class TwigNodeParser
{
    public function __construct(
        private SmartFileSystem $smartFileSystem
    ) {
    }

    /**
     * @return ModuleNode<Node>
     */
    public function parseFilePath(string $filePath): ModuleNode
    {
        $fileContent = $this->smartFileSystem->readFile($filePath);
        $tolerantTwigEnvironment = $this->createTwigEnvironment($filePath, $fileContent);
        $tokenStream = $tolerantTwigEnvironment->tokenize(new Source($fileContent, $filePath));

        return $tolerantTwigEnvironment->parse($tokenStream);
    }

    public function parseFilePathToPhpContent(string $filePath): string
    {
        $fileContent = $this->smartFileSystem->readFile($filePath);
        $tolerantTwigEnvironment = $this->createTwigEnvironment($filePath, $fileContent);
        $tokenStream = $tolerantTwigEnvironment->tokenize(new Source($fileContent, $filePath));

        $moduleNode = $tolerantTwigEnvironment->parse($tokenStream);

        return $tolerantTwigEnvironment->compile($moduleNode);
    }

    private function createTwigEnvironment(string $filePath, string $fileContent): TolerantTwigEnvironment
    {
        $arrayLoader = new ArrayLoader([
            $filePath => $fileContent,
        ]);

        return new TolerantTwigEnvironment($arrayLoader);
    }
}
