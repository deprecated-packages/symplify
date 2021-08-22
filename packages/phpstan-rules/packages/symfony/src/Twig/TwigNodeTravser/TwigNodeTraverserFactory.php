<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Twig\TwigNodeTravser;

use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\NodeTraverser;
use Twig\NodeVisitor\NodeVisitorInterface;

final class TwigNodeTraverserFactory
{
    /**
     * @param NodeVisitorInterface[] $nodeVisitors
     */
    public function createWithNodeVisitors(array $nodeVisitors): NodeTraverser
    {
        $environment = new Environment(new ArrayLoader());
        return new NodeTraverser($environment, $nodeVisitors);
    }
}
