<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\BoolishClassMethodPrefixRule\Fixture;

final class SkipClassWithEmptyReturn
{
    public function nothing(): void
    {
        return;
    }
}
