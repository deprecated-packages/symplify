<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\NodeAnalyzer\Template;

use Symplify\PHPStanRules\Symfony\Twig\TwigNodeParser;
use Symplify\PHPStanRules\Symfony\Twig\TwigNodeVisitor\VariableCollectingNodeVisitor;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\NodeTraverser;

final class TwigVariableNamesResolver
{
    public function __construct(
        private TwigNodeParser $twigNodeParser
    ) {
    }

    /**
     * @return string[]
     */
    public function resolveFromFile(string $filePath): array
    {
        $moduleNode = $this->twigNodeParser->parseFilePath($filePath);

        $variableCollectingNodeVisitor = new VariableCollectingNodeVisitor();
        $twigNodeTraverser = new NodeTraverser(new Environment(new ArrayLoader()), [$variableCollectingNodeVisitor]);

        $twigNodeTraverser->traverse($moduleNode);

        return $variableCollectingNodeVisitor->getVariableNames();
    }
}
