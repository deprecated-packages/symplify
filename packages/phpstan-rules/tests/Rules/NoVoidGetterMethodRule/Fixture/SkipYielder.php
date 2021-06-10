<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoVoidGetterMethodRule\Fixture;

use Iterator;

final class SkipYielder
{
    public function get(): Iterator
    {
        yield [200];
    }
}
