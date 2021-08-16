<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Complexity\NoDuplicatedArgumentRule\NoDuplicatedArgumentRuleTest
 */
final class NoDuplicatedArgumentRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'This call has duplicate argument';

    public function __construct(
        private NodeValueResolver $nodeValueResolver,
        private ReflectionProvider $reflectionProvider
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class, StaticCall::class, FuncCall::class];
    }

    /**
     * @param MethodCall|StaticCall|FuncCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($this->shouldSkip($node, $scope)) {
            return [];
        }

        $resolveValues = $this->resolvedValues($node, $scope->getFile());

        // filter out false/true values
        $resolvedValuesWithoutBool = \array_filter($resolveValues, fn ($value) => ! $this->shouldSkipValue($value));
        if ($resolvedValuesWithoutBool === []) {
            return [];
        }

        $countValues = $this->countValues($resolvedValuesWithoutBool);

        // each of kind
        if ($countValues === []) {
            return [];
        }

        $maxCountValues = max($countValues);
        if ($maxCountValues === 1) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            self::ERROR_MESSAGE,
            [new CodeSample(
                <<<'CODE_SAMPLE'
function run($one, $one);
CODE_SAMPLE
    ,
                <<<'CODE_SAMPLE'
function run($one, $two);
CODE_SAMPLE
            )]
        );
    }

    /**
     * @return mixed[]
     */
    private function resolvedValues(MethodCall|StaticCall|FuncCall $expr, string $filePath): array
    {
        $passedValues = [];
        foreach ($expr->args as $arg) {
            $resolvedValue = $this->nodeValueResolver->resolve($arg->value, $filePath);

            // unwrap single array item
            if (\is_array($resolvedValue) && \count($resolvedValue) === 1) {
                $resolvedValue = \array_pop($resolvedValue);
            }

            $passedValues[] = $resolvedValue;
        }

        return $passedValues;
    }

    private function shouldSkip(StaticCall|MethodCall|FuncCall $expr, Scope $scope): bool
    {
        if (\count($expr->args) < 2) {
            return true;
        }

        if ($expr instanceof FuncCall) {
            if (! $expr->name instanceof Name) {
                return true;
            }

            if (! $this->reflectionProvider->hasFunction($expr->name, $scope)) {
                return false;
            }

            // skip native functions
            $functionReflection = $this->reflectionProvider->getFunction($expr->name, $scope);
            return $functionReflection->isBuiltin();
        }

        return false;
    }

    private function shouldSkipValue(mixed $value): bool
    {
        // value could not be resolved
        if ($value === null) {
            return true;
        }

        if (is_array($value)) {
            return true;
        }

        // simple values, probably boolean markers or type constants
        if (\in_array($value, [0, 1], true)) {
            return true;
        }

        return \is_bool($value);
    }

    /**
     * @param mixed[] $values
     * @return mixed[]
     */
    private function countValues(array $values): array
    {
        if ($values === []) {
            return [];
        }

        // the array_count_values ignores "null", so we have to translate it to string here
        $values = array_filter($values, function (mixed $value) {
            return is_numeric($value) || is_string($value);
        });

        return \array_count_values($values);
    }
}
