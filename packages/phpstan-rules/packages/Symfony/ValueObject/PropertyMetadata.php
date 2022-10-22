<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\ValueObject;

use PHPStan\BetterReflection\Reflection\Adapter\ReflectionProperty;
use PHPStan\Reflection\Php\PhpPropertyReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\Type;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;

final class PropertyMetadata
{
    private PropertyReflection $phpstanPropertyReflection;

    private ReflectionProperty $nativeReflectionProperty;

    private int $propertyLine;

    public function __construct(
        PhpPropertyReflection $phpstanPropertyReflection,
        ReflectionProperty $nativeReflectionProperty,
        int $propertyLine
    ) {
        $this->phpstanPropertyReflection = $phpstanPropertyReflection;
        $this->nativeReflectionProperty = $nativeReflectionProperty;
        $this->propertyLine = $propertyLine;
    }

    public function getPropertyType(): Type
    {
        return $this->phpstanPropertyReflection->getReadableType();
    }

    public function getDocComment(): string
    {
        return (string) $this->phpstanPropertyReflection->getDocComment();
    }

    public function getFileName(): string
    {
        $reflectionClass = $this->nativeReflectionProperty->getDeclaringClass();

        $fileName = $reflectionClass->getFileName();
        if ($fileName === false) {
            throw new ShouldNotHappenException();
        }

        return $fileName;
    }

    public function getPropertyName(): string
    {
        return $this->nativeReflectionProperty->getName();
    }

    public function getPropertyLine(): int
    {
        return $this->propertyLine;
    }
}
