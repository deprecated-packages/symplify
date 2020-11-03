<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Symplify\PHPStanRules\ObjectCalisthenics\Marker\IndentationMarker;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;

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
        $statementDepth = $node->getAttribute(PHPStanAttributeKey::STATEMENT_DEPTH);
        if (! is_int($statementDepth)) {
            return null;
        }

        $this->indentationMarker->markIndentation($statementDepth);
        return null;
    }
}
