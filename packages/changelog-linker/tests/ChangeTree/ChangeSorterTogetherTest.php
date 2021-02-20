<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangeTree;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangeTree\ChangeSorter;
use Symplify\ChangelogLinker\ValueObject\PackageCategoryPriority;

final class ChangeSorterTogetherTest extends TestCase
{
    /**
     * @var ChangeSorter
     */
    private $changeSorter;

    /**
     * @var DummyChangesFactory
     */
    private $dummyChangesFactory;

    protected function setUp(): void
    {
        $this->changeSorter = new ChangeSorter();
        $this->dummyChangesFactory = new DummyChangesFactory();
    }

    /**
     * Tags should keep the same order for whatever priority
     * @dataProvider provideDataForTags()
     */
    public function testTags(string $priority): void
    {
        $changes = $this->dummyChangesFactory->create();
        $sortedChanges = $this->changeSorter->sort($changes, $priority);

        // unrelased are first
        for ($i = 0; $i <= 17; ++$i) {
            $this->assertSame('Unreleased', $sortedChanges[$i]->getTag());
        }

        $this->assertSame('v3.0.0', $sortedChanges[18]->getTag());
        $this->assertSame('v3.0.0', $sortedChanges[19]->getTag());

        // RC after stable
        $this->assertSame('v3.0.0-RC2', $sortedChanges[20]->getTag());
        $this->assertSame('v3.0.0-RC2', $sortedChanges[21]->getTag());

        $this->assertSame('v2.0', $sortedChanges[22]->getTag());
        $this->assertSame('v2.0', $sortedChanges[23]->getTag());
    }

    public function provideDataForTags(): Iterator
    {
        yield [PackageCategoryPriority::CATEGORIES];
        yield [PackageCategoryPriority::PACKAGES];
        yield [PackageCategoryPriority::NONE];
    }

    public function testSortWithCategoryPriority(): void
    {
        $changes = $this->dummyChangesFactory->create();

        $sortedChanges = $this->changeSorter->sort($changes, PackageCategoryPriority::CATEGORIES);

        $categoriesByTags = [];
        foreach ($sortedChanges as $sortedChange) {
            $categoriesByTags[$sortedChange->getTag()][] = $sortedChange->getCategory();
        }

        // test categories order under the tag
        $this->assertSame([
            'Added', 'Added', 'Added', 'Added',
            'Changed', 'Changed', 'Changed', 'Changed',
            'Fixed', 'Fixed', 'Fixed', 'Fixed', 'Fixed', 'Fixed', 'Fixed',
            'Removed', 'Removed',
            'Unknown Category',
        ], $categoriesByTags['Unreleased']);

        $this->assertSame(['Added', 'Changed'], $categoriesByTags['v3.0.0']);
        $this->assertSame(['Added', 'Changed'], $categoriesByTags['v3.0.0-RC2']);
        $this->assertSame(['Unknown Category', 'Unknown Category'], $categoriesByTags['v2.0']);

        // test package order inside tag, inside categories
        $unreleasedTagPackagesCategories = [];
        foreach ($sortedChanges as $sortedChange) {
            if ($sortedChange->getTag() !== 'Unreleased') {
                continue;
            }
            $unreleasedTagPackagesCategories[$sortedChange->getCategory()][] = $sortedChange->getPackage();
        }

        $this->assertSame(
            ['ChangelogLinker', 'ChangelogLinker', 'CodingStandard', 'Statie'],
            $unreleasedTagPackagesCategories['Added']
        );
        $this->assertSame(
            ['ChangelogLinker', 'Statie', 'Unknown Package', 'Unknown Package'],
            $unreleasedTagPackagesCategories['Changed']
        );
        $this->assertSame(
            [
                'BetterPhpDocParser',
                'ChangelogLinker',
                'ChangelogLinker',
                'ChangelogLinker',
                'CodingStandard',
                'Unknown Package',
                'Unknown Package',
            ],
            $unreleasedTagPackagesCategories['Fixed']
        );
        $this->assertSame(['ChangelogLinker', 'ChangelogLinker'], $unreleasedTagPackagesCategories['Removed']);
    }

    public function testSortWithPackagePriority(): void
    {
        $changes = $this->dummyChangesFactory->create();
        $sortedChanges = $this->changeSorter->sort($changes, PackageCategoryPriority::PACKAGES);

        $packagesByTags = [];
        foreach ($sortedChanges as $sortedChange) {
            $packagesByTags[$sortedChange->getTag()][] = $sortedChange->getPackage();
        }

        $this->assertSame([
            'BetterPhpDocParser',
            'ChangelogLinker',
            'ChangelogLinker',
            'ChangelogLinker',
            'ChangelogLinker',
            'ChangelogLinker',
            'ChangelogLinker',
            'ChangelogLinker',
            'ChangelogLinker',
            'CodingStandard',
            'CodingStandard',
            'Statie',
            'Statie',
            'Statie',
            'Unknown Package',
            'Unknown Package',
            'Unknown Package',
            'Unknown Package',
        ], $packagesByTags['Unreleased']);
    }
}
