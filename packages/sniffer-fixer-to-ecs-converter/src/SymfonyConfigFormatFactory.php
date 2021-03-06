<?php

declare(strict_types=1);

namespace Symplify\SnifferFixerToECSConverter;

use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use Symplify\PhpConfigPrinter\ValueObject\YamlKey;

final class SymfonyConfigFormatFactory
{
    /**
     * @param string[] $sniffClasses
     * @param string[] $setsParameter
     * @param array<string|int, mixed> $skipParameter
     * @param string[] $excludePathsParameter
     * @param string[] $pathsParameter
     * @return mixed[]
     */
    public function createSymfonyConfigFormat(
        array $sniffClasses,
        array $setsParameter,
        array $skipParameter,
        array $excludePathsParameter,
        array $pathsParameter
    ): array {
        $yaml = [];

        if ($sniffClasses !== []) {
            $yaml[YamlKey::SERVICES] = $sniffClasses;
        }

        if ($pathsParameter !== []) {
            $yaml[YamlKey::PARAMETERS][Option::class . '::PATHS'] = $pathsParameter;
        }

        $setsParameter = array_unique($setsParameter);
        foreach ($setsParameter as $set) {
            $yaml[YamlKey::PARAMETERS][Option::class . '::SETS'][] = SetList::class . '::' . $set;
        }

        if ($excludePathsParameter !== []) {
            $yaml[YamlKey::PARAMETERS][Option::class . '::EXCLUDE_PATHS'] = $excludePathsParameter;
        }

        if ($skipParameter !== []) {
            $yaml[YamlKey::PARAMETERS][Option::class . '::SKIP'] = $skipParameter;
        }

        return $yaml;
    }
}
