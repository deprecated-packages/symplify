<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoEmptyRule\Fixture;

final class EmptyCall
{
    public function run()
    {
        return empty($value);
    }
}
