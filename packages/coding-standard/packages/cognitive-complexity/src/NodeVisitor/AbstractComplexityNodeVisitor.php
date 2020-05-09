<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\CognitiveComplexity\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Stmt\Break_;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\Continue_;
use PhpParser\Node\Stmt\Do_;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Goto_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Switch_;
use PhpParser\Node\Stmt\While_;
use PhpParser\NodeVisitorAbstract;

abstract class AbstractComplexityNodeVisitor extends NodeVisitorAbstract
{
    /**
     * B1. Increments
     * @var class-string[]
     */
    private const BREAKING_NODE_TYPES = [Continue_::class, Goto_::class, Break_::class];

    /**
     * B1. Increments
     * @var class-string[]
     */
    private const INCREASING_NODE_TYPES = [
        If_::class,
        Else_::class,
        ElseIf_::class,
        Switch_::class,
        For_::class,
        Foreach_::class,
        While_::class,
        Do_::class,
        Catch_::class,
        // &&
        BooleanAnd::class,
        Ternary::class,
    ];

    /**
     * @param class-string[] $nodeTypes
     */
    protected function isNodeOfTypes(Node $node, array $nodeTypes): bool
    {
        foreach ($nodeTypes as $nodeType) {
            if (is_a($node, $nodeType, true)) {
                return true;
            }
        }

        return false;
    }

    protected function isIncrementingNode(Node $node): bool
    {
        // B1. ternary operator
        if ($this->isNodeOfTypes($node, self::INCREASING_NODE_TYPES)) {
            return true;
        }

        // B1. goto LABEL, break LABEL, continue LABEL
        if ($node instanceof Ternary) {
            return true;
        }

        if ($this->isBreakingNode($node)) {
            return true;
        }

        return false;
    }

    protected function isBreakingNode(Node $node): bool
    {
        // B1. goto LABEL, break LABEL, continue LABEL
        if ($this->isNodeOfTypes($node, self::BREAKING_NODE_TYPES)) {
            // skip empty breaks
            /** @var Goto_|Break_|Continue_ $node */
            if ($node instanceof Goto_ && $node->name !== null) {
                return true;
            }

            if (($node instanceof Break_ || $node instanceof Continue_) && $node->num !== null) {
                return true;
            }
        }

        return false;
    }
}
