<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ServiceOptionAnalyzer;

final class ServiceOptionAnalyzer
{
    /**
     * @param array<string|int, mixed> $data
     */
    public function hasNamedArguments(array $data): bool
    {
        if ($data === []) {
            return false;
        }

        foreach (array_keys($data) as $key) {
            if (! \str_starts_with((string) $key, '$')) {
                return false;
            }
        }

        return true;
    }
}
