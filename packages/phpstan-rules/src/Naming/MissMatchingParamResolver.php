<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Naming;

use Symplify\PHPStanRules\ValueObject\MissMatchingParamName;

final class MissMatchingParamResolver
{
    /**
     * @param string[] $currentParamNames
     * @param string[] $parentParamNames
     * @return MissMatchingParamName[]
     */
    public function resolve(array $currentParamNames, array $parentParamNames): array
    {
        $missMatchingParamNames = [];

        foreach ($currentParamNames as $key => $currentParamName) {
            if (! isset($parentParamNames[$key])) {
                continue;
            }

            $parentParamName = $parentParamNames[$key];
            if ($parentParamName === $currentParamName) {
                continue;
            }

            $missMatchingParamNames[] = new MissMatchingParamName($key, $currentParamName, $parentParamName);
        }

        return $missMatchingParamNames;
    }
}
