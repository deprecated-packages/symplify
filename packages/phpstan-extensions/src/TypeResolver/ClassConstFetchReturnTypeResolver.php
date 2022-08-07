<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\TypeResolver;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Symplify\Astral\Exception\ShouldNotHappenException;

final class ClassConstFetchReturnTypeResolver
{
    public function resolve(MethodReflection $methodReflection, MethodCall $methodCall): Type
    {
        $returnType = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();

        if (! isset($methodCall->args[0])) {
            throw new ShouldNotHappenException('Not supported without argument');
        }

        $firstArgOrVariadciPlaceholder = $methodCall->args[0];
        if (! $firstArgOrVariadciPlaceholder instanceof Arg) {
            return new MixedType();
        }

        $firstValue = $firstArgOrVariadciPlaceholder->value;
        if (! $firstValue instanceof ClassConstFetch) {
            return new MixedType();
        }

        $className = null;
        if ($firstValue->class instanceof Name && ($firstValue->name instanceof Identifier && $firstValue->name->toString() === 'class')) {
            $className = $firstValue->class->toString();
        }

        if ($className !== null) {
            return new ObjectType($className);
        }

        return $returnType;
    }
}
