<?php

use PhpParser\Node;

class SkipCallableHasSameParamPreviousDefinition
{
    /**
     * @param Node|Node[] $nodes
     * @param callable(Node $node): bool $filter
     */
    public function findFirst(Node | array $nodes, callable $filter): ?Node
    {

    }
}
?>
