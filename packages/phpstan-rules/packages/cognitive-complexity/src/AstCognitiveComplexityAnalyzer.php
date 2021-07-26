<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\PHPStanRules\CognitiveComplexity\DataCollector\CognitiveComplexityDataCollector;
use Symplify\PHPStanRules\CognitiveComplexity\NodeTraverser\ComplexityNodeTraverserFactory;
use Symplify\PHPStanRules\CognitiveComplexity\NodeVisitor\NestingNodeVisitor;

/**
 * @see \Symplify\PHPStanRules\CognitiveComplexity\Tests\AstCognitiveComplexityAnalyzer\AstCognitiveComplexityAnalyzerTest
 */
final class AstCognitiveComplexityAnalyzer
{
    private const NON_FINAL_CLASS_SCORE = 10;
    private const INHERITANCE_CLASS_SCORE = 25;

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

        // non final classes are more complex
        if (!$classLike->isFinal()) {
            $totalCognitiveComplexity += self::NON_FINAL_CLASS_SCORE;
        }

        // classes extending from another are more complex
        if ($classLike->extends !== null) {
            $totalCognitiveComplexity += self::INHERITANCE_CLASS_SCORE;
        }

        return $totalCognitiveComplexity;
    }

    public function analyzeFunctionLike(Function_ | ClassMethod $functionLike): int
    {
        $this->cognitiveComplexityDataCollector->reset();
        $this->nestingNodeVisitor->reset();

        $nodeTraverser = $this->complexityNodeTraverserFactory->create();
        $nodeTraverser->traverse([$functionLike]);

        return $this->cognitiveComplexityDataCollector->getCognitiveComplexity();
    }
}
