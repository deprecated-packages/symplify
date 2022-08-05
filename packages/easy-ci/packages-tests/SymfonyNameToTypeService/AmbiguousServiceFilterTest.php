<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\SymfonyNameToTypeService;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCI\SymfonyNameToTypeService\AmbiguousServiceFilter;

final class AmbiguousServiceFilterTest extends TestCase
{
    private AmbiguousServiceFilter $ambiguousServiceFilter;

    protected function setUp(): void
    {
        $this->ambiguousServiceFilter = new AmbiguousServiceFilter();
    }

    /**
     * @param array<string, string> $serviceMap
     * @param array<string, string> $expectedServiceMap
     *
     * @dataProvider provideData()
     */
    public function test(array $serviceMap, array $expectedServiceMap): void
    {
        $serviceMap = $this->ambiguousServiceFilter->filter($serviceMap);

        $this->assertSame($expectedServiceMap, $serviceMap);
    }

    public function provideData(): Iterator
    {
        yield [
            [
                'some.another' => 'App\AnotherType',
                'some.name' => 'App\SomeType',
                'some.name2' => 'App\SomeType',
                'some.name3' => 'App\SomeType',
            ],
            [
                'some.another' => 'App\AnotherType',
            ],
        ];
    }
}
