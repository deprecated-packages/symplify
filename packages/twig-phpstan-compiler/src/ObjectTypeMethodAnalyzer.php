<?php

declare(strict_types=1);

namespace Symplify\TwigPHPStanCompiler;

use PHPStan\Type\TypeWithClassName;

final class ObjectTypeMethodAnalyzer
{
    public function matchObjectTypeGetterName(TypeWithClassName $typeWithClassName, string $methodName): string|null
    {
        $possibleGetterMethodNames = $this->createPossibleMethodNames($methodName);

        foreach ($possibleGetterMethodNames as $possibleGetterMethodName) {
            if (! $typeWithClassName->hasMethod($possibleGetterMethodName)->yes()) {
                continue;
            }

            return $possibleGetterMethodName;
        }

        return null;
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
