<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Symplify\CodingStandard\ObjectCalisthenics\Marker\IndentationMarker;

final class IndentationNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var IndentationMarker
     */
    private $indentationMarker;

    public function __construct(IndentationMarker $indentationMarker)
    {
        $this->indentationMarker = $indentationMarker;
    }

    public function enterNode(Node $node)
    {
        $statementDepth = $node->getAttribute('statementDepth');
        if (! is_int($statementDepth)) {
            return null;
        }

        $this->indentationMarker->markIndentation($statementDepth);
        return null;
    }
}
