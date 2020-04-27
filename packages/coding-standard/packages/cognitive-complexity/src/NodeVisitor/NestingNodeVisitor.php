<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\CognitiveComplexity\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\Do_;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\While_;
use Symplify\CodingStandard\CognitiveComplexity\DataCollector\CognitiveComplexityDataCollector;

final class NestingNodeVisitor extends AbstractComplexityNodeVisitor
{
    /**
     * @var class-string[]
     */
    private const NESTING_NODE_TYPES = [
        If_::class,
        For_::class,
        While_::class,
        Catch_::class,
        Closure::class,
        Do_::class,
    ];

    /**
     * @var int
     */
    private $measuredNestingLevel = 0;

    /**
     * @var int
     */
    private $previousNestingLevel = 0;

    /**
     * @var CognitiveComplexityDataCollector
     */
    private $cognitiveComplexityDataCollector;

    public function __construct(CognitiveComplexityDataCollector $cognitiveComplexityDataCollector)
    {
        $this->cognitiveComplexityDataCollector = $cognitiveComplexityDataCollector;
    }

    public function enterNode(Node $node): ?Node
    {
        if ($this->isNestingNode($node)) {
            ++$this->measuredNestingLevel;
        }

        // B2. Nesting level
        if ($this->measuredNestingLevel > 1 && $this->previousNestingLevel < $this->measuredNestingLevel) {
            // only going deeper, not on the same level
            $nestingComplexity = $this->measuredNestingLevel - 1;
            $this->cognitiveComplexityDataCollector->increase($nestingComplexity);
        }

        $this->previousNestingLevel = $this->measuredNestingLevel;

        return null;
    }

    public function leaveNode(Node $node): ?Node
    {
        if ($this->isNodeOfTypes($node, self::NESTING_NODE_TYPES)) {
            --$this->measuredNestingLevel;
        }

        return null;
    }

    private function isNestingNode(Node $node): bool
    {
        return $this->isNodeOfTypes($node, self::NESTING_NODE_TYPES);
    }
}
