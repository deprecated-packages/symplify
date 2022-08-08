<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class EnumConstantAnalyzer
{
    public function isNonEnumConstantPrefix(string $prefix): bool
    {
        // constant prefix is needed
        if (! \str_ends_with($prefix, '_')) {
            return true;
        }

        return $this->isNonEnumConstantName($prefix);
    }

    private function isNonEnumConstantName(string $name): bool
    {
        // not enum, but rather validation limit
        if (\str_starts_with($name, 'MIN_')) {
            return true;
        }

        if (\str_ends_with($name, '_MIN')) {
            return true;
        }

        if (\str_starts_with($name, 'MAX_')) {
            return true;
        }

        return \str_ends_with($name, '_MAX');
    }
}
