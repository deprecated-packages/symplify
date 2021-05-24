<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoVoidGetterMethodRule\Fixture;

use InvalidArgumentException;

final class SkipNoThrows
{
    public function get(): int
    {
        // not supported
        throw new InvalidArgumentException();
    }
}
