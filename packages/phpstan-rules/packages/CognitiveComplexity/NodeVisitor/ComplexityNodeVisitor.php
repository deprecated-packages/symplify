<?php

declare(strict_types=1);

namespace NodeVisitor;

use DataCollector\CognitiveComplexityDataCollector;
use NodeAnalyzer\ComplexityAffectingNodeFinder;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

final class ComplexityNodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private CognitiveComplexityDataCollector $cognitiveComplexityDataCollector,
        private ComplexityAffectingNodeFinder $complexityAffectingNodeFinder
    ) {
    }

    public function enterNode(Node $node): ?Node
    {
        if (! $this->complexityAffectingNodeFinder->isIncrementingNode($node)) {
            return null;
        }

        $this->cognitiveComplexityDataCollector->increaseOperation();

        return null;
    }
}
