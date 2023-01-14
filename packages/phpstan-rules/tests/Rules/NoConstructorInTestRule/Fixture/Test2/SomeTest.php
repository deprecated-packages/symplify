<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoConstructorInTestRule\Fixture\Test2;

use PHPUnit\Framework\TestCase;
use stdClass;

final class SomeTest extends TestCase
{
    public function __construct()
    {
        $this->obj = new stdClass;
    }
}
