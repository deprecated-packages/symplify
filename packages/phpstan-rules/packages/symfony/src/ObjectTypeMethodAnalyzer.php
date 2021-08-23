<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony;

use PHPStan\Type\TypeWithClassName;

final class ObjectTypeMethodAnalyzer
{
    public function hasObjectTypeMagicGetter(TypeWithClassName $typeWithClassName, string $methodName): bool
    {
        $possibleGetterMethodNames = $this->createPossibleMethodNames($methodName);
        return $this->hasObjectTypeAnyMethod($possibleGetterMethodNames, $typeWithClassName);
    }

    /**
     * @param string[] $possibleGetterMethodNames
     */
    private function hasObjectTypeAnyMethod(
        array $possibleGetterMethodNames,
        TypeWithClassName $typeWithClassName
    ): bool {
        foreach ($possibleGetterMethodNames as $possibleGetterMethodName) {
            if (! $typeWithClassName->hasMethod($possibleGetterMethodName)->yes()) {
                continue;
            }

            return true;
        }

        return false;
    }

    /**
     * @see https://stackoverflow.com/a/25409133/1348344
     *
     * @return string[]
     */
    private function createPossibleMethodNames(string $methodName): array
    {
        return [$methodName, 'get' . ucfirst($methodName), 'is' . ucfirst($methodName)];
    }
}
