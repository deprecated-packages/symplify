<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\TypeExtension\FuncCall;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;

/**
 * @see \Symplify\PHPStanExtensions\Tests\TypeExtension\FuncCall\NativeFunctionDynamicFunctionReturnTypeExtension\NativeFunctionDynamicFunctionReturnTypeExtensionTest
 */
final class NativeFunctionDynamicFunctionReturnTypeExtension implements DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array($functionReflection->getName(), ['getcwd', 'dirname'], true);
    }

    public function getTypeFromFunctionCall(
        FunctionReflection $functionReflection,
        FuncCall $funcCall,
        Scope $scope
    ): Type {
        return new StringType();
    }
}
