<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PreferredRawDataInTestDataProviderRule\Fixture;

use stdClass;

final class UseRawDataForTestDataProviderTest
{
    private $obj;

    protected function setUp()
    {
        $this->obj = new stdClass;
    }

    public function provideFoo()
    {
        return [
            [[true]]
        ];
    }

    /**
     * @dataProvider provideFoo
     */
    public function testFoo($value)
    {
        $this->obj->x = $value;
        $this->assertTrue($this->obj->x);
    }
}
