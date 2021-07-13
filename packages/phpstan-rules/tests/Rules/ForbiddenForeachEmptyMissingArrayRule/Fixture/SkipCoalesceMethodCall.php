<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenCallOnTypeRule\Fixture;

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
