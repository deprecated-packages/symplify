<?php

declare(strict_types=1);

namespace Symplify\Statie\Tests\Templating;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\Templating\ArrayUtils;

final class ArraySorterTest extends TestCase
{
    /**
     * @var string[][]
     */
    private const FIXTURE_ITEMS = [
        ['name' => 'b'],
        ['name' => 'b'],
        ['name' => 'a'],
        ['name' => 'c'],
    ];

    /**
     * @var ArrayUtils
     */
    private $arrayUtils;

    protected function setUp(): void
    {
        $this->arrayUtils = new ArrayUtils();
    }

    public function testGroupByField(): void
    {
        $groupedItems = $this->arrayUtils->groupByField(self::FIXTURE_ITEMS, 'name');

        $expectedGroupedItems = [
            'a' => [
                ['name' => 'a'],
            ],
            'b' => [
                ['name' => 'b'],
                ['name' => 'b'],
            ],
            'c' => [
                ['name' => 'c'],
            ],
        ];

        $this->assertSame($expectedGroupedItems, $groupedItems);
    }

    public function testSortByField(): void
    {
        $sortedItems = $this->arrayUtils->sortByField(self::FIXTURE_ITEMS, 'name');
        $expectedSortedItems = [
            ['name' => 'a'],
            ['name' => 'b'],
            ['name' => 'b'],
            ['name' => 'c'],
        ];
        $this->assertSame($expectedSortedItems, $sortedItems);

        $sortedItems = $this->arrayUtils->sortByField(self::FIXTURE_ITEMS, 'name', 'desc');
        $expectedSortedItems = [
            ['name' => 'c'],
            ['name' => 'b'],
            ['name' => 'b'],
            ['name' => 'a'],
        ];
        $this->assertSame($expectedSortedItems, $sortedItems);
    }
}
