<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ArrayType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoArrayStringObjectReturnRule\NoArrayStringObjectReturnRuleTest
 */
final class NoArrayStringObjectReturnRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use another value object over string with value object arrays';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class, Variable::class, PropertyFetch::class];
    }

    /**
     * @param MethodCall|Variable|PropertyFetch $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node instanceof MethodCall) {
            return $this->processMethodCall($node, $scope);
        }

        $variableType = $scope->getType($node);
        if (! $this->isArrayStringToObjetType($variableType)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    /**
     * @return string[]
     */
    private function processMethodCall(MethodCall $methodCall, Scope $scope): array
    {
        $methodCallReturnType = $scope->getType($methodCall);
        if (! $this->isArrayStringToObjetType($methodCallReturnType)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function isArrayStringToObjetType(Type $type): bool
    {
        if (! $type instanceof ArrayType) {
            return false;
        }

        if (! $type->getKeyType() instanceof StringType) {
            return false;
        }

        return $type->getItemType() instanceof ObjectType;
    }
}
