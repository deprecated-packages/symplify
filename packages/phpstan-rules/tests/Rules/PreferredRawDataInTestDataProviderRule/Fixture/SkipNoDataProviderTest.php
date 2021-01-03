<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredRawDataInTestDataProviderRule\Fixture;

use stdClass;

final class SkipNoDataProviderTest
{
    private $obj;

    protected function setUp()
    {
        $this->obj = new stdClass;
    }

    public function testFoo()
    {
        $this->obj->x = true;
        $this->assertTrue($this->obj->x);
    }
}
