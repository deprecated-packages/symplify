<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symfony\Component\Console\Command\Command;
use Symplify\Astral\Naming\SimpleNameResolver;
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
    public const ERROR_MESSAGE = 'Class %s cognitive complexity is %d, keep it under %d';

    /**
     * @param array<string, int> $limitsByTypes
     */
    public function __construct(
        private AstCognitiveComplexityAnalyzer $astCognitiveComplexityAnalyzer,
        private CompositionOverInheritanceAnalyzer $compositionOverInheritanceAnalyzer,
        private SimpleNameResolver $simpleNameResolver,
        private int $maxClassCognitiveComplexity = 50,
        private array $limitsByTypes = [],
        private bool $scoreCompositionOverInheritance = false
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return ClassLike::class;
    }

    /**
     * @param ClassLike $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node instanceof Class_) {
            return [];
        }

        if ($this->scoreCompositionOverInheritance) {
            $measuredCognitiveComplexity = $this->compositionOverInheritanceAnalyzer->analyzeClassLike($node);
        } else {
            $measuredCognitiveComplexity = $this->astCognitiveComplexityAnalyzer->analyzeClassLike($node);
        }

        $allowedCognitiveComplexity = $this->resolveAllowedCognitiveComplexity($node);
        if ($measuredCognitiveComplexity <= $allowedCognitiveComplexity) {
            return [];
        }

        $message = sprintf(self::ERROR_MESSAGE, $measuredCognitiveComplexity, $allowedCognitiveComplexity);

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

    private function resolveAllowedCognitiveComplexity(Class_ $class): int
    {
        $className = $this->simpleNameResolver->getName($class);
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
