<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckParentChildMethodParameterTypeCompatibleRule\Fixture;

use stdClass;

interface AnInterfaceOther
{
    public function run(stdClass $stdClass);
}
