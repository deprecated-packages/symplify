<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\CognitiveComplexity\AstCognitiveComplexityAnalyzer;
use Symplify\PHPStanRules\CognitiveComplexity\CompositionOverInheritanceAnalyzer;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule\ClassLikeCognitiveComplexityRuleTest
 */
final class ClassLikeCognitiveComplexityRule implements Rule, DocumentedRuleInterface, ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class cognitive complexity is %d, keep it under %d';

    public function __construct(
        private AstCognitiveComplexityAnalyzer $astCognitiveComplexityAnalyzer,
        private CompositionOverInheritanceAnalyzer $compositionOverInheritanceAnalyzer,
        private int $maxClassCognitiveComplexity = 50,
        private bool $scoreCompositionOverInheritance = false
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classLike = $node->getOriginalNode();
        if (! $classLike instanceof Class_) {
            return [];
        }

        if ($this->scoreCompositionOverInheritance) {
            $measuredCognitiveComplexity = $this->compositionOverInheritanceAnalyzer->analyzeClassLike($classLike);
        } else {
            $measuredCognitiveComplexity = $this->astCognitiveComplexityAnalyzer->analyzeClassLike($classLike);
        }

        if ($measuredCognitiveComplexity <= $this->maxClassCognitiveComplexity) {
            return [];
        }

        $message = sprintf(self::ERROR_MESSAGE, $measuredCognitiveComplexity, $this->maxClassCognitiveComplexity);

        return [$message];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Cognitive complexity of class/trait must be under specific limit',
            [new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function simple($value)
    {
        if ($value !== 1) {
            if ($value !== 2) {
                return false;
            }
        }

        return true;
    }

    public function another($value)
    {
        if ($value !== 1 && $value !== 2) {
            return false;
        }

        return true;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function simple($value)
    {
        return $this->someOtherService->count($value);
    }

    public function another($value)
    {
        return $this->someOtherService->delete($value);
    }
}
CODE_SAMPLE
                ,
                [
                    'maxClassCognitiveComplexity' => 10,
                    'scoreCompositionOverInheritance' => true,
                ]
            )]
        );
    }
}
