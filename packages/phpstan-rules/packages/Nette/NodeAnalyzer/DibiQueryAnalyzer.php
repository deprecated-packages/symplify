<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\NodeAnalyzer;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use Symplify\Astral\Naming\SimpleNameResolver;

final class DibiQueryAnalyzer
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    public function isDibiConnectionQueryCall(Scope $scope, MethodCall $methodCall): bool
    {
        $callerType = $scope->getType($methodCall->var);

        $dibiConnectionObjectType = new ObjectType('Dibi\Connection');
        if (! $callerType->isSuperTypeOf($dibiConnectionObjectType)->yes()) {
            return false;
        }

        // check direct caller with string masks
        return $this->simpleNameResolver->isNames($methodCall->name, ['query']);
    }
}
