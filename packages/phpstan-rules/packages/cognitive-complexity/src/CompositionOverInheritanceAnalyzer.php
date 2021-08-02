<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\TraitUse;

/**
 * @see Symplify\PHPStanRules\CognitiveComplexity\Tests\CompositionOverInheritanceAnalyzer\CompositionOverInheritanceAnalyzerTest;
 */
final class CompositionOverInheritanceAnalyzer
{
    /**
     * @var int
     */
    private const NON_FINAL_CLASS_SCORE = 10;

    /**
     * @var int
     */
    private const TRAIT_SCORE = 10;

    /**
     * @var int
     */
    private const INHERITANCE_CLASS_SCORE = 25;

    public function __construct(
        private AstCognitiveComplexityAnalyzer $astCognitiveComplexityAnalyzer
    ) {
    }

    public function analyzeClassLike(ClassLike $classLike): int
    {
        $totalCognitiveComplexity = $this->astCognitiveComplexityAnalyzer->analyzeClassLike($classLike);

        // traits don't define isFinal()
        if (! $classLike instanceof Class_) {
            return $totalCognitiveComplexity;
        }

        // non final classes are more complex
        if (! $classLike->isFinal()) {
            $totalCognitiveComplexity += self::NON_FINAL_CLASS_SCORE;
        }

        // classes extending from another are more complex
        if ($classLike->extends !== null) {
            $totalCognitiveComplexity += self::INHERITANCE_CLASS_SCORE;
        }

        // classes using traits are more complex
        $totalCognitiveComplexity += $this->analyzeTraitUses($classLike);

        return $totalCognitiveComplexity;
    }

    private function analyzeTraitUses(Class_ $class): int
    {
        $traitComplexity = 0;

        if ($class->stmts) {
            foreach ($class->stmts as $stmt) {
                // trait-use can only appear as the very first statement in a class
                if ($stmt instanceof TraitUse) {
                    $traitComplexity += count($stmt->traits) * self::TRAIT_SCORE;
                } else {
                    break;
                }
            }
        }

        return $traitComplexity;
    }
}
