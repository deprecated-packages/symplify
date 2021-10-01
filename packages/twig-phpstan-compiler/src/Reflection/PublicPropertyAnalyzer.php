<?php

declare(strict_types=1);

namespace Symplify\TwigPHPStanCompiler\Reflection;

use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;
use ReflectionProperty;

final class PublicPropertyAnalyzer
{
    /**
     * @var array<string, array<string, bool>>
     */
    private array $resolvedPropertyVisibility = [];

    public function hasPublicProperty(Type $type, string $variableName): bool
    {
        if (! $type instanceof TypeWithClassName) {
            return false;
        }

        if (! $type->hasProperty($variableName)->yes()) {
            return false;
        }

        $cachedResolvedVisibility = $this->resolvedPropertyVisibility[$type->getClassName()][$variableName] ?? null;
        if ($cachedResolvedVisibility !== null) {
            return $cachedResolvedVisibility;
        }

        if (! property_exists($type->getClassName(), $variableName)) {
            return false;
        }

        $reflectionProperty = new ReflectionProperty($type->getClassName(), $variableName);
        $resolvedVisibility = $reflectionProperty->isPublic();

        $this->resolvedPropertyVisibility[$type->getClassName()][$variableName] = $resolvedVisibility;
        return $resolvedVisibility;
    }
}
