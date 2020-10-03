<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PreferredRawDataInTestDataProviderRule\Fixture;

final class UseDataFromSetupInTestDataProviderTest
{
    private $data;

    protected function setUp()
    {
        $this->data = true;
    }

    public function provideFoo()
    {
        yield [$this->data];
    }

    /**
     * @dataProvider provideFoo
     */
    public function testFoo($value)
    {
        $this->assertTrue($value);
    }
}
