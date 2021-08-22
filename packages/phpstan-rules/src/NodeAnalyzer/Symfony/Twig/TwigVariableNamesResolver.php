<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\Symfony\Twig;

use Symplify\PHPStanRules\Twig\NodeVisitor\VariableCollectingNodeVisitor;
use Symplify\PHPStanRules\Twig\TwigNodeParser;
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
