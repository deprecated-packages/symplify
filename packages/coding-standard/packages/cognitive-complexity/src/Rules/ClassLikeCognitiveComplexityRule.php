<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\CognitiveComplexity\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\CognitiveComplexity\AstCognitiveComplexityAnalyzer;
use Symplify\CodingStandard\Rules\AbstractManyNodeTypeRule;

/**
 * @see \Symplify\CodingStandard\CognitiveComplexity\Tests\Rules\ClassLikeCognitiveComplexityRule\ClassLikeCognitiveComplexityRuleTest
 */
final class ClassLikeCognitiveComplexityRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '%s cognitive complexity for "%s" is %d, keep it under %d';

    /**
     * @var int
     */
    private $maximumClassCognitiveComplexity;

    /**
     * @var AstCognitiveComplexityAnalyzer
     */
    private $astCognitiveComplexityAnalyzer;

    public function __construct(
        AstCognitiveComplexityAnalyzer $astCognitiveComplexityAnalyzer,
        int $maximumClassCognitiveComplexity = 50
    ) {
        $this->maximumClassCognitiveComplexity = $maximumClassCognitiveComplexity;
        $this->astCognitiveComplexityAnalyzer = $astCognitiveComplexityAnalyzer;
    }

    /**
     * @return class-string[]
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
        foreach ($node->getMethods() as $classMethod) {
            $classLikeCognitiveComplexity += $this->astCognitiveComplexityAnalyzer->analyzeFunctionLike($classMethod);
        }

        if ($classLikeCognitiveComplexity <= $this->maximumClassCognitiveComplexity) {
            return [];
        }

        $classLikeName = (string) $node->name;

        $type = $node instanceof Class_ ? 'Class' : 'Trait';

        $message = sprintf(
            self::ERROR_MESSAGE,
            $type,
            $classLikeName,
            $classLikeCognitiveComplexity,
            $this->maximumClassCognitiveComplexity
        );

        return [$message];
    }
}
