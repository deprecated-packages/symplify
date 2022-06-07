<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredRawDataInTestDataProviderRule\Fixture;

use PHPUnit\Framework\TestCase;

final class UseDataFromSetupInTestDataProviderTest extends TestCase
{
    private $data;

    protected function setUp(): void
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
