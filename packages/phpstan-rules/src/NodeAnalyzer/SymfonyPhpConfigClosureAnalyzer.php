<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\Native\NativeParameterReflection;
use PHPStan\Type\ObjectType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class SymfonyPhpConfigClosureAnalyzer
{
    public function isSymfonyPhpConfigScope(Scope $scope): bool
    {
        // we are in a closure
        if ($scope->getAnonymousFunctionReflection() === null) {
            return false;
        }

        if (count($scope->getAnonymousFunctionReflection()->getParameters()) !== 1) {
            return false;
        }

        /** @var NativeParameterReflection $onlyParameter */
        $onlyParameter = $scope->getAnonymousFunctionReflection()
            ->getParameters()[0];
        $onlyParameterType = $onlyParameter->getType();

        $containerConfiguratorObjectType = new ObjectType(ContainerConfigurator::class);
        return $onlyParameterType->isSuperTypeOf($containerConfiguratorObjectType)
            ->yes();
    }
}
