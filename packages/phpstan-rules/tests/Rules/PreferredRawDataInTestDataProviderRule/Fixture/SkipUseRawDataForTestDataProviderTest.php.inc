<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredRawDataInTestDataProviderRule\Fixture;

use PHPUnit\Framework\TestCase;
use stdClass;

final class SkipUseRawDataForTestDataProviderTest extends TestCase
{
    private $obj;

    protected function setUp(): void
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
