<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\NodeAnalyzer;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;

final class DibiQueryAnalyzer
{
    public function isDibiConnectionQueryCall(Scope $scope, MethodCall $methodCall): bool
    {
        $callerType = $scope->getType($methodCall->var);

        $dibiConnectionObjectType = new ObjectType('Dibi\Connection');
        if (! $callerType->isSuperTypeOf($dibiConnectionObjectType)->yes()) {
            return false;
        }

        if (! $methodCall->name instanceof Identifier) {
            return false;
        }

        // check direct caller with string masks
        return $methodCall->name->toString() === 'query';
    }
}
