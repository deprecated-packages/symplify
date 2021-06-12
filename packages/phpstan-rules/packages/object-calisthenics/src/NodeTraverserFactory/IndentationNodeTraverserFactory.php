<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\NodeTraverserFactory;

use PhpParser\NodeTraverser;
use Symplify\PHPStanRules\ObjectCalisthenics\NodeVisitor\IndentationNodeVisitor;

final class IndentationNodeTraverserFactory
{
    public function __construct(
        private IndentationNodeVisitor $indentationNodeVisitor
    ) {
    }

    public function create(): NodeTraverser
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($this->indentationNodeVisitor);

        return $nodeTraverser;
    }
}
