<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture;

use DateTime;
use stdClass;

final class SkipUsedInternallyForSecondType
{
    public function run(stdClass|DateTime $obj)
    {
    }

    public function execute()
    {
        $this->run(new DateTime('now'));
    }
}
