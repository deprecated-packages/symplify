<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;
use Symfony\Component\Console\Command\Command;
use Symplify\PHPStanRules\CognitiveComplexity\AstCognitiveComplexityAnalyzer;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\CognitiveComplexity\Tests\Rules\ClassLikeCognitiveComplexityRule\ClassLikeCognitiveComplexityRuleTest
 */
final class ClassLikeCognitiveComplexityRule extends AbstractSymplifyRule implements DocumentedRuleInterface, ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '%s cognitive complexity is %d, keep it under %d';

    /**
     * @var int
     */
    private $maxClassCognitiveComplexity;

    /**
     * @var AstCognitiveComplexityAnalyzer
     */
    private $astCognitiveComplexityAnalyzer;

    /**
     * @var array<string, int>
     */
    private $limitsByTypes = [];

    /**
     * @param array<string, int> $limitsByTypes
     */
    public function __construct(
        AstCognitiveComplexityAnalyzer $astCognitiveComplexityAnalyzer,
        int $maxClassCognitiveComplexity = 50,
        array $limitsByTypes = []
    ) {
        $this->maxClassCognitiveComplexity = $maxClassCognitiveComplexity;
        $this->astCognitiveComplexityAnalyzer = $astCognitiveComplexityAnalyzer;
        $this->limitsByTypes = $limitsByTypes;
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
        $measuredCognitiveComplexity = $this->astCognitiveComplexityAnalyzer->analyzeClassLike($node);

        $allowedCognitiveComplexity = $this->resolveAllowedCognitiveComplexity($scope, $node);
        if ($measuredCognitiveComplexity <= $allowedCognitiveComplexity) {
            return [];
        }

        $type = $node instanceof Class_ ? 'Class' : 'Trait';
        $message = sprintf(self::ERROR_MESSAGE, $type, $measuredCognitiveComplexity, $allowedCognitiveComplexity);

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
                ]
            ),
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
use Symfony\Component\Console\Command\Command;

class SomeCommand extends Command
{
    public function configure()
    {
        $this->setName('...');
    }

    public function execute()
    {
        if (...) {
            // ...
        } else {
            // ...
        }
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use Symfony\Component\Console\Command\Command;

class SomeCommand extends Command
{
    public function configure()
    {
        $this->setName('...');
    }

    public function execute()
    {
        return $this->externalService->resolve(...);
    }
}
CODE_SAMPLE
                    ,
                    [
                        'limitsByTypes' => [
                            Command::class => 5,
                        ],
                    ]
                ),

            ]
        );
    }

    private function resolveAllowedCognitiveComplexity(Scope $scope, ClassLike $classLike): int
    {
        $className = $this->getClassName($scope, $classLike);
        if ($className === null) {
            return $this->maxClassCognitiveComplexity;
        }

        foreach ($this->limitsByTypes as $type => $limit) {
            if (is_a($className, $type, true)) {
                return $limit;
            }
        }

        return $this->maxClassCognitiveComplexity;
    }
}
