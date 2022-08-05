<?php

declare(strict_types=1);

namespace Symplify\EasyCI\SymfonyNameToTypeService;

/**
 * @see \Symplify\EasyCI\Tests\SymfonyNameToTypeService\AmbiguousServiceFilterTest
 */
final class AmbiguousServiceFilter
{
    /**
     * @param array<string, string> $serviceTypesByName
     * @return array<string, string>
     */
    public function filter(array $serviceTypesByName): array
    {
        $ambiguousTypes = $this->resolveAmbiguousTypes($serviceTypesByName);

        // remove duplicated types
        foreach ($serviceTypesByName as $name => $type) {
            if (! in_array($type, $ambiguousTypes, true)) {
                continue;
            }

            unset($serviceTypesByName[$name]);
        }

        return $serviceTypesByName;
    }

    /**
     * @param array<string, string> $serviceTypesByName
     * @return string[]
     */
    private function resolveAmbiguousTypes(array $serviceTypesByName): array
    {
        // check if some types are duplicated, and remove those!
        $serviceTypeCountValues = array_count_values($serviceTypesByName);

        $ambiguousTypes = [];
        foreach ($serviceTypeCountValues as $type => $count) {
            if ($count === 1) {
                continue;
            }

            $ambiguousTypes[] = $type;
        }

        return $ambiguousTypes;
    }
}
