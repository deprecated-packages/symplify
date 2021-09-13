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
    private TolerantTwigEnvironment $tolerantTwigEnvironment;

    public function __construct(
        private SmartFileSystem $smartFileSystem,
    ) {
        $this->tolerantTwigEnvironment = new TolerantTwigEnvironment(new ArrayLoader());
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

        $this->tolerantTwigEnvironment->setLoader($arrayLoader);

        $tokenStream = $this->tolerantTwigEnvironment->tokenize(new Source($fileContent, $filePath));

        return $this->tolerantTwigEnvironment->parse($tokenStream);
    }

    public function compileFilePath(string $templateFilePath): string
    {
        $moduleNode = $this->parseFilePath($templateFilePath);
        return $this->tolerantTwigEnvironment->compile($moduleNode);
    }
}
