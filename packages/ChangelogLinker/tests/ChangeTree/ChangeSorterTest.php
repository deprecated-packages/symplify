<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangeTree;

use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangeTree\Change;
use Symplify\ChangelogLinker\ChangeTree\ChangeSorter;

final class ChangeSorterTest extends TestCase
{
    /**
     * @var ChangeSorter
     */
    private $changeSorter;

    protected function setUp(): void
    {
        $this->changeSorter = new ChangeSorter();
    }

    public function testSortWithCategoryPriority(): void
    {
        $changes = $this->createChanges();

        $sortedChanges = $this->changeSorter->sortByCategoryAndPackage($changes, false);
        $this->assertNotSame($changes, $sortedChanges);

        $firstChange = array_shift($sortedChanges);
        $this->assertSame('Added', $firstChange->getCategory());
        $this->assertSame('B', $firstChange->getPackage());

        $secondChange = array_shift($sortedChanges);
        $this->assertSame('Changed', $secondChange->getCategory());
        $this->assertSame('B', $secondChange->getPackage());
    }

    public function testSortWithPackagePriority(): void
    {
        $changes = $this->createChanges();

        $sortedChanges = $this->changeSorter->sortByCategoryAndPackage($changes, true);
        $this->assertNotSame($changes, $sortedChanges);

        $firstChange = array_shift($sortedChanges);
        $this->assertSame('A', $firstChange->getPackage());
        $this->assertSame('Removed', $firstChange->getCategory());

        $secondChange = array_shift($sortedChanges);
        $this->assertSame('B', $secondChange->getPackage());
        $this->assertSame('Added', $secondChange->getCategory());
    }

    private function createChanges(): array
    {
        $changes = [
            new Change('message', 'Changed', 'B'),
            new Change('message', 'Added', 'B'),
            new Change('message', 'Removed', 'A')
        ];
        return $changes;
    }
}
