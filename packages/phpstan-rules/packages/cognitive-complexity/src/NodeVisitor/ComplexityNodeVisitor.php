<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Symplify\PHPStanRules\CognitiveComplexity\DataCollector\CognitiveComplexityDataCollector;
use Symplify\PHPStanRules\CognitiveComplexity\NodeAnalyzer\ComplexityAffectingNodeFinder;

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
