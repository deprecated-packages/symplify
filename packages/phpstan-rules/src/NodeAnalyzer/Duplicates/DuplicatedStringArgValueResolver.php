<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\Duplicates;

use Symplify\PHPStanRules\ValueObject\Duplicates\DuplicatedStringArg;

final class DuplicatedStringArgValueResolver
{
    /**
     * @var string[]
     */
    private array $alreadyReportedUniqueIds = [];

    /**
     * @param array<string, string[]> $stringValuesByUniqueId
     */
    public function resolve(array $stringValuesByUniqueId, int $allowedLimit): ?DuplicatedStringArg
    {
        foreach ($stringValuesByUniqueId as $uniqueId => $stringValues) {
            // skip reported cases
            if (in_array($uniqueId, $this->alreadyReportedUniqueIds, true)) {
                continue;
            }

            $valuesToCount = array_count_values($stringValues);
            foreach ($valuesToCount as $value => $count) {
                if ($count <= $allowedLimit) {
                    continue;
                }

                $this->alreadyReportedUniqueIds[] = $uniqueId;
                return new DuplicatedStringArg($value, $count);
            }
        }

        return null;
    }
}
