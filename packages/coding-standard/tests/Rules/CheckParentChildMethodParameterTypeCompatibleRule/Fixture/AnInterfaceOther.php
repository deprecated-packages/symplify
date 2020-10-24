<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckParentChildMethodParameterTypeCompatibleRule\Fixture;

use stdClass;

interface AnInterfaceOther
{
    public function run(stdClass $stdClass);
}
