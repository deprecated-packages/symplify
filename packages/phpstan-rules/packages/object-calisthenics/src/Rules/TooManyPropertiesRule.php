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
 * @see \Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\TooManyPropertiesRule\TooManyPropertiesRuleTest
 *
 * @deprecated This rule is rather academic and does not relate ot complexity. Use
 * @see ClassLikeCognitiveComplexityRule instead
 */
final class TooManyPropertiesRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class has too many properties %d. Try narrowing it down under %d';

    public function __construct(
        private int $maxPropertyCount = 10
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
        $currentPropertyCount = count($node->getProperties());
        if ($currentPropertyCount < $this->maxPropertyCount) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $currentPropertyCount, $this->maxPropertyCount);
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    private $some;

    private $another;

    private $third;
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    private $some;

    private $another;
}
CODE_SAMPLE
                ,
                [
                    'maxPropertyCount' => 2,
                ]
            ),
        ]);
    }
}
