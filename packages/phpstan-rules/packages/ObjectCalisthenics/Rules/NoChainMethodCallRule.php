<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\Enum\AttributeKey;
use Symplify\PHPStanRules\NodeAnalyzer\MethodCall\AllowedChainCallSkipper;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see https://github.com/object-calisthenics/phpcs-calisthenics-rules#5-use-only-one-object-operator---per-statement
 *
 * @see \Symplify\PHPStanRules\Tests\ObjectCalisthenics\Rules\NoChainMethodCallRule\NoChainMethodCallRuleTest
 * @implements Rule<MethodCall>
 */
final class NoChainMethodCallRule implements Rule, DocumentedRuleinterface, ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use chained method calls. Put each on separated lines.';

    /**
     * @param class-string[] $allowedChainTypes
     */
    public function __construct(
        private AllowedChainCallSkipper $allowedChainCallSkipper,
        private array $allowedChainTypes = [],
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->var instanceof MethodCall) {
            return [];
        }

        // skip nullsafe chain
        $isNullsafeChecked = (bool) $node->var->getAttribute(AttributeKey::NULLSAFE_CHECKED);
        if ($isNullsafeChecked) {
            return [];
        }

        if ($this->allowedChainCallSkipper->isAllowedFluentMethodCall($scope, $node, $this->allowedChainTypes)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
$this->runThis()->runThat();

$fluentClass = new AllowedFluent();
$fluentClass->one()->two();
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$this->runThis();
$this->runThat();

$fluentClass = new AllowedFluent();
$fluentClass->one()->two();
CODE_SAMPLE
                ,
                [
                    'allowedChainTypes' => ['AllowedFluent'],
                ]
            ),
        ]);
    }
}
