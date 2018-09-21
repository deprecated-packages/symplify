<?php declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Symfony provided Symfony\Component\Finder\SplFileInfo always exists,
 * so checking every single $splFileInfo->getRealPath() has no added value.
 * Just pollutes code and config and makes it unreadable.
 *
 * This narrows validation only to custom created SplFileInfo.
 */
final class SplFileInfoTolerantDynamicMethodReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return SplFileInfo::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'getRealPath';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        return new StringType();
    }
}
