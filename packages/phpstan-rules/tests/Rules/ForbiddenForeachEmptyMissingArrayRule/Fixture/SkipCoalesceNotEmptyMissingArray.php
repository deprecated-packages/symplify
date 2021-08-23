<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenForeachEmptyMissingArrayRule\Fixture;

final class SkipCoalesceNotEmptyMissingArray
{
    public function run(array $data)
    {
        foreach ($data ?? ['test'] as $value) {
        }
    }
}
