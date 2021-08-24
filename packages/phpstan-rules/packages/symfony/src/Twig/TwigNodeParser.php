<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Twig;

use Symplify\SmartFileSystem\SmartFileSystem;
use Twig\Loader\ArrayLoader;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\Source;

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

        $arrayLoader = new ArrayLoader([
            $filePath => $fileContent,
        ]);

        $tolerantTwigEnvironment = new TolerantTwigEnvironment($arrayLoader);
        $tokenStream = $tolerantTwigEnvironment->tokenize(new Source($fileContent, $filePath));

        return $tolerantTwigEnvironment->parse($tokenStream);
    }
}
