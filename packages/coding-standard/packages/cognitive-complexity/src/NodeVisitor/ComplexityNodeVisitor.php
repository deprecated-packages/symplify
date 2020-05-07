<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\CognitiveComplexity\NodeVisitor;

use PhpParser\Node;
use Symplify\CodingStandard\CognitiveComplexity\DataCollector\CognitiveComplexityDataCollector;

final class ComplexityNodeVisitor extends AbstractComplexityNodeVisitor
{
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
        if (! $this->isIncrementingNode($node)) {
            return null;
        }

        $this->cognitiveComplexityDataCollector->increaseOperation();

        return null;
    }
}
