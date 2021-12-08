<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenParamTypeRemovalRule\Fixture;

use stdClass;

interface AnInterfaceOther
{
    public function run(stdClass $stdClass);
}
