<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticPropertyRule\Fixture;

final class SomeFinalClassUsesTrait
{
    use SomeTrait;

    protected $x;

    protected function run()
    {
    }
}
