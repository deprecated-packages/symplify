<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedAssignRule\Fixture;

final class SkipArrayFilterValues
{
    public function run()
    {
        $values = $this->getValues();

        $values = array_filter($values);

        $values = array_values($values);
    }

    public function getValues(): array
    {
        return [1, 2, 3];
    }
}
