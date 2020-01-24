<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Php\Type;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\ResourceType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;

final class NativeFunctionDynamicFunctionReturnTypeExtension implements DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array($functionReflection->getName(), ['getcwd', 'tmpfile', 'dirname'], true);
    }

    public function getTypeFromFunctionCall(
        FunctionReflection $functionReflection,
        FuncCall $funcCall,
        Scope $scope
    ): Type {
        if ($functionReflection->getName() === 'tmpfile') {
            return new ResourceType();
        }

        return new StringType();
    }
}
