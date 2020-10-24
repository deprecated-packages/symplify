<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckParentChildMethodParameterTypeCompatibleRule\Fixture;

use stdClass;

class HasSameParameterWithInterfaceMethod implements AnInterface, AnInterfaceOther
{
    public function execute(string $string, int $int)
    {
    }

    public function run(stdClass $stdClass)
    {
    }
}
