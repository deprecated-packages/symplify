<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\NodeTraverserFactory;

use PhpParser\NodeTraverser;
use Symplify\PHPStanRules\ObjectCalisthenics\NodeVisitor\IndentationNodeVisitor;

final class IndentationNodeTraverserFactory
{
    /**
     * @var IndentationNodeVisitor
     */
    private $indentationNodeVisitor;

    public function __construct(IndentationNodeVisitor $indentationNodeVisitor)
    {
        $this->indentationNodeVisitor = $indentationNodeVisitor;
    }

    public function create(): NodeTraverser
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($this->indentationNodeVisitor);

        return $nodeTraverser;
    }
}
