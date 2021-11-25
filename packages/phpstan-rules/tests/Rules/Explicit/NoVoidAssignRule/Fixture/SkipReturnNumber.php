<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoVoidAssignRule\Fixture;

final class SkipReturnNumber
{
    public function run()
    {
        $value = $this->getSomething();
    }

    public function getSomething(): int
    {
        return 1000;
    }
}
