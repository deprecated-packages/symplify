<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoParticularNodeRule\Fixture;

final class EmptyCall
{
    public function run()
    {
        return empty($value);
    }
}
