<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoConstructorInTestRule\Fixture\Test3;

use stdClass;

final class SkipTestWithoutParentTest
{
    public function __construct()
    {
        $this->obj = new stdClass;
    }
}
