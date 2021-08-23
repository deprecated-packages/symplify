<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Twig;

use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symplify\PHPStanRules\Symfony\Twig\DummyService\DummyUrlGenerator;
use Symplify\SmartFileSystem\SmartFileSystem;
use Twig\Environment;
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

        $environment = new Environment($arrayLoader);
        // basic extensions, to allow parsing templates - possibly re-use from the project itself
        $environment->addExtension(new FormExtension());
        $environment->addExtension(new RoutingExtension(new DummyUrlGenerator()));

        $tokenStream = $environment->tokenize(new Source($fileContent, $filePath));

        return $environment->parse($tokenStream);
    }
}
