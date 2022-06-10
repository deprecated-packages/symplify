<?php

declare(strict_types=1);

namespace NodeTraverser;

use NodeVisitor\ComplexityNodeVisitor;
use NodeVisitor\NestingNodeVisitor;
use PhpParser\NodeTraverser;

final class ComplexityNodeTraverserFactory
{
    public function __construct(
        private NestingNodeVisitor $nestingNodeVisitor,
        private ComplexityNodeVisitor $complexityNodeVisitor
    ) {
    }

    public function create(): NodeTraverser
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($this->nestingNodeVisitor);
        $nodeTraverser->addVisitor($this->complexityNodeVisitor);

        return $nodeTraverser;
    }
}
