<?php

declare(strict_types=1);

namespace Symplify\TwigPHPStanCompiler\Reflection;

use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;
use ReflectionProperty;

final class PublicPropertyAnalyzer
{
    public function hasPublicProperty(Type $type, string $variableName): bool
    {
        if (! $type instanceof TypeWithClassName) {
            return false;
        }

        if (! $type->hasProperty($variableName)->yes()) {
            return false;
        }

        if (! property_exists($type->getClassName(), $variableName)) {
            return false;
        }

        $reflectionProperty = new ReflectionProperty($type->getClassName(), $variableName);
        return $reflectionProperty->isPublic();
    }
}
