<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\CognitiveComplexity;

use PhpParser\Node\FunctionLike;
use PhpParser\NodeTraverser;
use Symplify\CodingStandard\CognitiveComplexity\DataCollector\CognitiveComplexityDataCollector;
use Symplify\CodingStandard\CognitiveComplexity\NodeVisitor\ComplexityNodeVisitor;
use Symplify\CodingStandard\CognitiveComplexity\NodeVisitor\NestingNodeVisitor;

final class AstCognitiveComplexityAnalyzer
{
    /**
     * @var CognitiveComplexityDataCollector
     */
    private $cognitiveComplexityDataCollector;

    /**
     * @var NestingNodeVisitor
     */
    private $nestingNodeVisitor;

    /**
     * @var ComplexityNodeVisitor
     */
    private $complexityNodeVisitor;

    public function __construct(
        CognitiveComplexityDataCollector $cognitiveComplexityDataCollector,
        NestingNodeVisitor $nestingNodeVisitor,
        ComplexityNodeVisitor $complexityNodeVisitor
    ) {
        $this->cognitiveComplexityDataCollector = $cognitiveComplexityDataCollector;
        $this->nestingNodeVisitor = $nestingNodeVisitor;
        $this->complexityNodeVisitor = $complexityNodeVisitor;
    }

    public function analyzeFunctionLike(FunctionLike $functionLike): int
    {
        $this->cognitiveComplexityDataCollector->reset();

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($this->nestingNodeVisitor);
        $nodeTraverser->addVisitor($this->complexityNodeVisitor);
        $nodeTraverser->traverse([$functionLike]);

        return $this->cognitiveComplexityDataCollector->getCognitiveComplexity();
    }
}
