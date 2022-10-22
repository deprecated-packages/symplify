<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony;

use PHPStan\Analyser\Scope;
use PHPStan\BetterReflection\BetterReflection;
use PHPStan\BetterReflection\Reflection\ReflectionProperty;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\Php\PhpPropertyReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\ShouldNotHappenException;
use Symplify\PHPStanRules\Symfony\ValueObject\PropertyMetadata;

final class PropertyMetadataResolver
{
    /**
     * @return PropertyMetadata[]
     */
    public function resolvePropertyMetadatas(ClassReflection $classReflection, Scope $scope): array
    {
        $propertyMetadatas = [];

        $nativeClassReflection = $classReflection->getNativeReflection();

        foreach ($nativeClassReflection->getProperties() as $nativeReflectionProperty) {
            $propertyName = $nativeReflectionProperty->getName();

            $phpstanPropertyReflection = $classReflection->getProperty($propertyName, $scope);
            $propertyLine = $this->resolvePropertyLine($phpstanPropertyReflection, $propertyName);

            /** @var PhpPropertyReflection $phpstanPropertyReflection */
            $propertyMetadatas[] = new PropertyMetadata(
                $phpstanPropertyReflection,
                $nativeReflectionProperty,
                $propertyLine
            );
        }

        return $propertyMetadatas;
    }

    private function resolvePropertyLine(PropertyReflection $phpstanPropertyReflection, string $propertyName): int
    {
        $declaringClassReflection = $phpstanPropertyReflection->getDeclaringClass();

        $reflector = (new BetterReflection())->reflector();

        $betterClassReflection = $reflector->reflectClass($declaringClassReflection->getName());
        if (! $betterClassReflection->hasProperty($propertyName)) {
            throw new ShouldNotHappenException();
        }

        $betterPropertyReflection = $betterClassReflection->getProperty($propertyName);
        if (! $betterPropertyReflection instanceof ReflectionProperty) {
            throw new ShouldNotHappenException();
        }

        return $betterPropertyReflection->getStartLine();
    }
}
