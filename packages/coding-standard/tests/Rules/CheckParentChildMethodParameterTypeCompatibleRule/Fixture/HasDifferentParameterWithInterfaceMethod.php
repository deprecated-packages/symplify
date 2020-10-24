<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckParentChildMethodParameterTypeCompatibleRule\Fixture;

use stdClass;

class HasDifferentParameterWithInterfaceMethod implements AnInterface, AnInterfaceOther
{
    public function execute($string, $int)
    {
    }

    public function run($stdClass)
    {
    }
}
