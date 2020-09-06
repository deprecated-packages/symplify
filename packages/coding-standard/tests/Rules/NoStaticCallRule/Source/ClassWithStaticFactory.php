<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticCallRule\Source;

final class ClassWithStaticFactory
{
    public static function create()
    {
        return new self();
    }
}
