<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Closure;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Type\CallableType;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\NoDynamicNameRuleTest
 */
final class NoDynamicNameRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use explicit names over dynamic ones';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [
            MethodCall::class,
            StaticCall::class,
            FuncCall::class,
            StaticPropertyFetch::class,
            PropertyFetch::class,
            ClassConstFetch::class,
        ];
    }

    /**
     * @param MethodCall|StaticCall|FuncCall|StaticPropertyFetch|PropertyFetch|ClassConstFetch $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node instanceof ClassConstFetch) {
            if (! $node->class instanceof Expr) {
                return [];
            }

            return [self::ERROR_MESSAGE];
        }

        if ($node instanceof StaticPropertyFetch) {
            if (! $node->class instanceof Expr) {
                return [];
            }

            return [self::ERROR_MESSAGE];
        } elseif (! $node->name instanceof Expr) {
            return [];
        }

        if ($this->isClosureOrCallableType($scope, $node->name)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function old(): bool
    {
        return $this->${variable};
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function old(): bool
    {
        return $this->specificMethodName();
    }
}
CODE_SAMPLE
            ),
        ]);
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

        return $nameStaticType->getClassName() === Closure::class;
    }

    private function unwrapNullableType(Type $type): Type
    {
        if (! $type instanceof UnionType) {
            return $type;
        }

        $unionedTypes = $type->getTypes();
        if (count($unionedTypes) !== 2) {
            return $type;
        }

        $nullSuperTypeTrinaryLogic = $type->isSuperTypeOf(new NullType());
        if (! $nullSuperTypeTrinaryLogic->yes()) {
            return $type;
        }

        foreach ($unionedTypes as $unionedType) {
            if ($unionedType instanceof NullType) {
                continue;
            }

            return $unionedType;
        }

        return $type;
    }
}
