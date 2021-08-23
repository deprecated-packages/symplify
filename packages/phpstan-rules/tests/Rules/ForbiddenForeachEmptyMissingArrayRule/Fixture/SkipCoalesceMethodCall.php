<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenForeachEmptyMissingArrayRule\Fixture;

final class SkipCoalesceMethodCall
{
    public function run(array $data)
    {
        foreach ($data ?? $this->fallback() as $value) {
        }
    }

    private function fallback(): array
    {
        return [];
    }
}
