<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Templating;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\Templating\ArraySorter;

final class ArraySorterTest extends TestCase
{
    /**
     * @var ArraySorter
     */
    private $arraySorter;

    protected function setUp(): void
    {
        $this->arraySorter = new ArraySorter();
    }

    public function test(): void
    {
        $items = [
            ['name' => 'b'],
            ['name' => 'a'],
            ['name' => 'c'],
        ];

        $sortedItems = $this->arraySorter->sortByField($items, 'name');
        $expectedSortedItems = [
            ['name' => 'a'],
            ['name' => 'b'],
            ['name' => 'c'],
        ];
        $this->assertSame($expectedSortedItems, $sortedItems);

        $sortedItems = $this->arraySorter->sortByField($items, 'name', 'desc');
        $expectedSortedItems = [
            ['name' => 'c'],
            ['name' => 'b'],
            ['name' => 'a'],
        ];
        $this->assertSame($expectedSortedItems, $sortedItems);
    }
}
