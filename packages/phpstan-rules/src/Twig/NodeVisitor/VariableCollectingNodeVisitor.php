<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Twig\NodeVisitor;

use Twig\Environment;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

final class VariableCollectingNodeVisitor implements NodeVisitorInterface
{
    /**
     * @var string[]
     */
    private $variableNames = [];

    /**
     * @param Node<Node> $node
     * @return Node<Node>
     */
    public function enterNode(Node $node, Environment $environment): Node
    {
        if (! $node instanceof NameExpression) {
            return $node;
        }

        $this->variableNames[] = $node->getAttribute('name');
        return $node;
    }

    /**
     * @param Node<Node> $node
     * @return Node<Node>|null
     */
    public function leaveNode(Node $node, Environment $environment): ?Node
    {
        return $node;
    }

    public function getPriority(): int
    {
        return 0;
    }

    /**
     * @return string[]
     */
    public function getVariableNames(): array
    {
        return $this->variableNames;
    }
}
