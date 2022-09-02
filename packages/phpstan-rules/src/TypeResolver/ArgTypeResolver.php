<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TypeResolver;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\Type;

final class ArgTypeResolver
{
    /**
     * @return Type[]
     */
    public function resolveArgTypesWithoutFirst(FuncCall $funcCall, Scope $scope): array
    {
        $args = $funcCall->getArgs();
        unset($args[0]);

        $argTypes = [];
        foreach ($args as $arg) {
            $argTypes[] = $scope->getType($arg->value);
        }

        return $argTypes;
    }
}
