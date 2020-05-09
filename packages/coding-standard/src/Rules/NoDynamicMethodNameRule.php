<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\CallableType;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoDynamicMethodNameRule\NoDynamicMethodNameRuleTest
 */
final class NoDynamicMethodNameRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use explicit method names over dynamic';

    /**
     * @param MethodCall|StaticCall|FuncCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Expr) {
            return [];
        }

        if ($this->isClosureOrCallableType($scope, $node->name)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    /**
     * @return class-string[]
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class, StaticCall::class, FuncCall::class];
    }

    private function isClosureOrCallableType(Scope $scope, Expr $expr): bool
    {
        $nameStaticType = $scope->getType($expr);

        $nameStaticType = $this->unwrapNullableType($nameStaticType);

        if ($nameStaticType instanceof CallableType) {
            return true;
        }

        if (! $nameStaticType instanceof ObjectType) {
            return false;
        }

        return $nameStaticType->getClassName() === 'Closure';
    }

    private function unwrapNullableType(Type $type): Type
    {
        if (! $type instanceof UnionType) {
            return $type;
        }

        if (count($type->getTypes()) !== 2) {
            return $type;
        }

        if (! $type->isSuperTypeOf(new NullType())->yes()) {
            return $type;
        }

        foreach ($type->getTypes() as $unionedType) {
            if ($unionedType instanceof NullType) {
                continue;
            }

            return $unionedType;
        }

        return $type;
    }
}
