<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\ValueObject;

use PHPStan\BetterReflection\Reflection\Adapter\ReflectionProperty;
use PHPStan\Reflection\Php\PhpPropertyReflection;
use PHPStan\Type\Type;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;

final class PropertyMetadata
{
    public function __construct(
        private PhpPropertyReflection $phpPropertyReflection,
        private ReflectionProperty $nativeReflectionProperty,
        private int $propertyLine
    ) {
    }

    public function getPropertyType(): Type
    {
        return $this->phpPropertyReflection->getReadableType();
    }

    public function getDocComment(): string
    {
        return (string) $this->phpPropertyReflection->getDocComment();
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
