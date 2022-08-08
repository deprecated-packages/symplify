<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TypeAnalyzer;

use PhpParser\Node\Expr;
use PHPStan\Analyser\Scope;
use PHPStan\Type\CallableType;
use PHPStan\Type\ClosureType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Symplify\PHPStanRules\Enum\MethodName;

final class CallableTypeAnalyzer
{
    public function isClosureOrCallableType(Scope $scope, Expr $expr): bool
    {
        $nameStaticType = $scope->getType($expr);
        $unwrappedNameStaticType = TypeCombinator::removeNull($nameStaticType);

        if ($unwrappedNameStaticType instanceof CallableType) {
            return true;
        }

        if ($unwrappedNameStaticType instanceof ClosureType) {
            return true;
        }

        return $this->isInvokableObjectType($unwrappedNameStaticType);
    }

    private function isInvokableObjectType(Type $type): bool
    {
        if (! $type instanceof ObjectType) {
            return false;
        }

        return $type->hasMethod(MethodName::INVOKE)->yes();
    }
}
