<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\CognitiveComplexity\AstCognitiveComplexityAnalyzer;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\CognitiveComplexity\Tests\Rules\ClassLikeCognitiveComplexityRule\ClassLikeCognitiveComplexityRuleTest
 */
final class ClassLikeCognitiveComplexityRule extends AbstractSymplifyRule implements DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '%s cognitive complexity for "%s" is %d, keep it under %d';

    /**
     * @var int
     */
    private $maxClassCognitiveComplexity;

    /**
     * @var AstCognitiveComplexityAnalyzer
     */
    private $astCognitiveComplexityAnalyzer;

    public function __construct(
        AstCognitiveComplexityAnalyzer $astCognitiveComplexityAnalyzer,
        int $maxClassCognitiveComplexity = 50
    ) {
        $this->maxClassCognitiveComplexity = $maxClassCognitiveComplexity;
        $this->astCognitiveComplexityAnalyzer = $astCognitiveComplexityAnalyzer;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Class_::class, Trait_::class];
    }

    /**
     * @param Class_|Trait_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $classLikeCognitiveComplexity = 0;

        $classMethods = $node->getMethods();
        foreach ($classMethods as $classMethod) {
            $classLikeCognitiveComplexity += $this->astCognitiveComplexityAnalyzer->analyzeFunctionLike($classMethod);
        }

        if ($classLikeCognitiveComplexity <= $this->maxClassCognitiveComplexity) {
            return [];
        }

        $classLikeName = (string) $node->name;

        $type = $node instanceof Class_ ? 'Class' : 'Trait';

        $message = sprintf(
            self::ERROR_MESSAGE,
            $type,
            $classLikeName,
            $classLikeCognitiveComplexity,
            $this->maxClassCognitiveComplexity
        );

        return [$message];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Cognitive complexity of class/trait must be under specific limit',
            [new CodeSample(
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
```
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
            )]
        );
    }
}
