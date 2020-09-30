<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckRequireMethodTobeAutowireWithClassName\Fixture;

final class WithoutRequired
{
    /**
     * @param int $foo
     */
    public function run(int $foo)
    {
    }
}
