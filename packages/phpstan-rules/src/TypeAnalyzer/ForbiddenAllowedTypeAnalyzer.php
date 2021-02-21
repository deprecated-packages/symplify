<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TypeAnalyzer;

use Symplify\PackageBuilder\Php\TypeChecker;

final class ForbiddenAllowedTypeAnalyzer
{
    /**
     * @var TypeChecker
     */
    private $typeChecker;

    public function __construct(TypeChecker $typeChecker)
    {
        $this->typeChecker = $typeChecker;
    }

    /**
     * @param string[] $forbiddenTypes
     * @param string[] $allowedTypes
     */
    public function shouldSkip(string $mainType, array $forbiddenTypes, array $allowedTypes): bool
    {
        if ($this->isAllowedType($mainType, $allowedTypes)) {
            return true;
        }

        return ! $this->isForbiddenType($mainType, $forbiddenTypes);
    }

    private function isForbiddenType(string $typeName, array $forbiddenTypes): bool
    {
        if ($forbiddenTypes === []) {
            return true;
        }

        return $this->typeChecker->isInstanceOf($typeName, $forbiddenTypes);
    }

    /**
     * @param string[] $allowedTypes
     */
    private function isAllowedType(string $typeName, array $allowedTypes): bool
    {
        return $this->typeChecker->isInstanceOf($typeName, $allowedTypes);
    }
}
