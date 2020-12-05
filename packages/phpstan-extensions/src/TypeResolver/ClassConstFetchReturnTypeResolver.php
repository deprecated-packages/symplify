<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\TypeResolver;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

final class ClassConstFetchReturnTypeResolver
{
    public function resolve(MethodReflection $methodReflection, MethodCall $methodCall): Type
    {
        $returnType = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
        if (! isset($methodCall->args[0])) {
            return $returnType;
        }

        $className = $this->resolveClassName($methodCall->args[0]->value);
        if ($className !== null) {
            return new ObjectType($className);
        }

        return $returnType;
    }

    private function resolveClassName(Expr $expr): ?string
    {
        if ($expr instanceof ClassConstFetch) {
            if ($expr->class instanceof Name) {
                return $expr->class->toString();
            }

            return null;
        }

        return null;
    }
}
