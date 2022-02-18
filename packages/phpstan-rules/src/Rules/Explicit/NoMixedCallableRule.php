<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Type\CallableType;
use PHPStan\Type\MixedType;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedCallableRule\NoMixedCallableRuleTest
 */
final class NoMixedCallableRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Make callable of param more explicit';

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Variable::class];
    }

    /**
     * @param Variable $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $variableType = $scope->getType($node);
        if (! $variableType instanceof CallableType) {
            return [];
        }

        // some params are defined, good
        if ($variableType->getParameters() !== []) {
            return [];
        }

        if ($variableType->getReturnType() instanceof MixedType) {
            return [self::ERROR_MESSAGE];
        }

        // check non-empty params also

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            self::ERROR_MESSAGE,
            [new CodeSample(
                <<<'CODE_SAMPLE'
function run(callable $callable)
{
    return $callable(100);
}
CODE_SAMPLE
,
                <<<'CODE_SAMPLE'
/**
 * @param callable(): int $callable
 */
function run(callable $callable): int
{
    return $callable(100);
}
CODE_SAMPLE
            )]
        );
    }
}
