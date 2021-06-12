<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity;

use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use Symplify\PHPStanRules\CognitiveComplexity\DataCollector\CognitiveComplexityDataCollector;
use Symplify\PHPStanRules\CognitiveComplexity\NodeTraverser\ComplexityNodeTraverserFactory;
use Symplify\PHPStanRules\CognitiveComplexity\NodeVisitor\NestingNodeVisitor;

/**
 * @see \Symplify\PHPStanRules\CognitiveComplexity\Tests\AstCognitiveComplexityAnalyzer\AstCognitiveComplexityAnalyzerTest
 */
final class AstCognitiveComplexityAnalyzer
{
    public function __construct(
        private ComplexityNodeTraverserFactory $complexityNodeTraverserFactory,
        private CognitiveComplexityDataCollector $cognitiveComplexityDataCollector,
        private NestingNodeVisitor $nestingNodeVisitor
    ) {
    }

    public function analyzeClassLike(ClassLike $classLike): int
    {
        $totalCognitiveComplexity = 0;
        foreach ($classLike->getMethods() as $classMethod) {
            $totalCognitiveComplexity += $this->analyzeFunctionLike($classMethod);
        }

        return $totalCognitiveComplexity;
    }

    /**
     * @param Function_|ClassMethod $functionLike
     */
    public function analyzeFunctionLike(FunctionLike $functionLike): int
    {
        $this->cognitiveComplexityDataCollector->reset();
        $this->nestingNodeVisitor->reset();

        $nodeTraverser = $this->complexityNodeTraverserFactory->create();
        $nodeTraverser->traverse([$functionLike]);

        return $this->cognitiveComplexityDataCollector->getCognitiveComplexity();
    }
}
