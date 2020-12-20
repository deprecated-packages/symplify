<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\BoolishClassMethodPrefixRule\Fixture;

final class SkipNestedCallback
{
    public function run()
    {
        $value = function (): bool {
            return true;
        };
    }
}
