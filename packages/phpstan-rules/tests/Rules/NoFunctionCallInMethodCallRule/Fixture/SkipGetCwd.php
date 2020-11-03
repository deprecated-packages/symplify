<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFunctionCallInMethodCallRule\Fixture;

final class SkipGetCwd
{
    public function run($value): void
    {
        $this->nothing(getcwd());
    }

    private function nothing($value)
    {
        return $value;
    }
}
