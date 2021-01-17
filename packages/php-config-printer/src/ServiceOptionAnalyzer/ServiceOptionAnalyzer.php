<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ServiceOptionAnalyzer;

use Nette\Utils\Strings;

final class ServiceOptionAnalyzer
{
    public function hasNamedArguments(array $data): bool
    {
        if ($data === []) {
            return false;
        }

        foreach (array_keys($data) as $key) {
            if (! Strings::startsWith((string) $key, '$')) {
                return false;
            }
        }

        return true;
    }
}
