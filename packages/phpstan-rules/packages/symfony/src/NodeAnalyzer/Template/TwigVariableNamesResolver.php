<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\NodeAnalyzer\Template;

use Symplify\PHPStanRules\Symfony\Twig\TwigNodeTravser\TwigNodeTraverserFactory;
use Symplify\PHPStanRules\Symfony\Twig\TwigNodeVisitor\VariableCollectingNodeVisitor;
use Symplify\PHPStanRules\TwigPHPStanPrinter\Twig\TwigNodeParser;

final class TwigVariableNamesResolver
{
    public function __construct(
        private TwigNodeParser $twigNodeParser,
        private TwigNodeTraverserFactory $twigNodeTraverserFactory
    ) {
    }

    /**
     * @return string[]
     */
    public function resolveFromFile(string $filePath): array
    {
        $moduleNode = $this->twigNodeParser->parseFilePath($filePath);

        // @todo use PHP traverser to unite with other behavior?

        $variableCollectingNodeVisitor = new VariableCollectingNodeVisitor();

        $twigNodeTraverser = $this->twigNodeTraverserFactory->createWithNodeVisitors([$variableCollectingNodeVisitor]);
        $twigNodeTraverser->traverse($moduleNode);

        return $variableCollectingNodeVisitor->getVariableNames();
    }
}
