<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment;
use Symplify\PHPStanRules\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\TooManyMethodsRule\TooManyMethodsRuleTest
 *
 * @deprecated This rule is rather academic and does not relate ot complexity. Use
 * @see ClassLikeCognitiveComplexityRule instead
 */
final class TooManyMethodsRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method has too many methods %d. Try narrowing it down under %d';

    public function __construct(
        private int $maxMethodCount = 15
    ) {
        if (! StaticPHPUnitEnvironment::isPHPUnitRun()) {
            $errorMessage = sprintf(
                '[Deprecated] This rule is rather academic and does not relate ot complexity. Use
     "%s" instead',
                ClassLikeCognitiveComplexityRule::class
            );
            trigger_error($errorMessage);
            sleep(3);
        }
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $currentMethodCount = count($node->getMethods());
        if ($currentMethodCount < $this->maxMethodCount) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $currentMethodCount, $this->maxMethodCount);
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function firstMethod()
    {
    }

    public function secondMethod()
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function firstMethod()
    {
    }
}
CODE_SAMPLE
                ,
                [
                    'maxMethodCount' => 1,
                ]
            ),
        ]);
    }
}
