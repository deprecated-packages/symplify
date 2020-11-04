<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see https://github.com/object-calisthenics/phpcs-calisthenics-rules#5-use-only-one-object-operator---per-statement
 *
 * @see \Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\NoChainMethodCallRule\NoChainMethodCallRuleTest
 */
final class NoChainMethodCallRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use chained method calls';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->var instanceof MethodCall) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$this->runThis()->runThat();
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$this->runThis();
$this->runThat();
CODE_SAMPLE
            ),
        ]);
    }
}
