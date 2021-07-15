<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TypeAnalyzer;

use Symplify\PackageBuilder\Php\TypeChecker;

final class ForbiddenAllowedTypeAnalyzer
{
    public function __construct(
        private TypeChecker $typeChecker
    ) {
    }

    /**
     * @param class-string[] $forbiddenTypes
     * @param class-string[] $allowedTypes
     */
    public function shouldSkip(string $mainType, array $forbiddenTypes, array $allowedTypes): bool
    {
        if ($this->typeChecker->isInstanceOf($mainType, $allowedTypes)) {
            return true;
        }

        return ! $this->isForbiddenType($mainType, $forbiddenTypes);
    }

    /**
     * @param class-string[] $forbiddenTypes
     */
    private function isForbiddenType(string $typeName, array $forbiddenTypes): bool
    {
        if ($forbiddenTypes === []) {
            return true;
        }

        return $this->typeChecker->isInstanceOf($typeName, $forbiddenTypes);
    }
}
