<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoDefaultExceptionRule\Fixture;

use RuntimeException;

final class ThrowGenericException
{
    public function run(): void
    {
        throw new RuntimeException();
    }
}
