<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangeTree;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangeTree\Change;
use Symplify\ChangelogLinker\ChangeTree\ChangeSorter;

final class ChangeSorterTogetherTest extends TestCase
{
    /**
     * @var ChangeSorter
     */
    private $changeSorter;

    protected function setUp(): void
    {
        $this->changeSorter = new ChangeSorter();
    }

    /**
     * Tags should keep the same order for whatever priority
     * @dataProvider provideDataForTags()
     */
    public function testTags(?string $priority): void
    {
        $changes = $this->createChanges();
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
        yield [ChangeSorter::PRIORITY_CATEGORIES];
        yield [ChangeSorter::PRIORITY_PACKAGES];
        yield [null];
    }

    public function testSortWithCategoryPriority(): void
    {
        $changes = $this->createChanges();

        $sortedChanges = $this->changeSorter->sort($changes, ChangeSorter::PRIORITY_CATEGORIES);

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
        $changes = $this->createChanges();

        $sortedChanges = $this->changeSorter->sort($changes, ChangeSorter::PRIORITY_PACKAGES);

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

    /**
     * Real life example
     *
     * @return Change[]
     */
    private function createChanges(): array
    {
        return [
            new Change(
                '- [#879] [ChangelogLinker] Add --token option to increase Github API rate [closes #874]',
                'Added',
                'ChangelogLinker',
                '- [#879] Add --token option to increase Github API rate [closes #874]',
                'v3.0.0'
            ),
            new Change(
                '- [#876] [ChangelogLinker] Fixes based on feedback',
                'Fixed',
                'ChangelogLinker',
                '- [#876] Fixes based on feedback',
                'Unreleased'
            ),
            new Change(
                '- [#893] [Statie] Rename FlatWhite to Latte and move Latte-related code there',
                'Unknown Category',
                'Statie',
                '- [#893] Rename FlatWhite to Latte and move Latte-related code there',
                'v2.0'
            ),
            new Change(
                '- [#888]  [Statie] Return collector-based approach to FileDecorators, with priorities',
                'Unknown Category',
                'Statie',
                '- [#888]  Return collector-based approach to FileDecorators, with priorities',
                'v2.0'
            ),
            new Change(
                '- [#905] [ChangelogLinker] Drop commit referencing to stprevent promoting my bad practise',
                'Removed',
                'ChangelogLinker',
                '- [#905] Drop commit referencing to stprevent promoting my bad practise',
                'Unreleased'
            ),
            new Change(
                '- [#885] [ChangelogLinker] Drop ReleaseReferencesWorker - replaced by dump-mer…',
                'Removed',
                'ChangelogLinker',
                '- [#885] Drop ReleaseReferencesWorker - replaced by dump-mer…',
                'Unreleased'
            ),
            new Change(
                '- [#875] Fixes monorepo splitting by travis cron job, Thanks to @JanMikes',
                'Fixed',
                'Unknown Package',
                '- [#875] Fixes monorepo splitting by travis cron job, Thanks to @JanMikes',
                'Unreleased'
            ),
            new Change(
                '- [#870] RemoveUselessDocBlockFixer should not reformat custom annotations, Thanks to @jankonas',
                'Fixed',
                'Unknown Package',
                '- [#870] RemoveUselessDocBlockFixer should not reformat custom annotations, Thanks to @jankonas',
                'Unreleased'
            ),
            new Change(
                '- [#901]  [CodingStandard] Allow list option in ClassNameSuffixByParentFixer',
                'Fixed',
                'CodingStandard',
                '- [#901]  Allow list option in ClassNameSuffixByParentFixer',
                'Unreleased'
            ),
            new Change(
                '- [#878] [ChangelogLinker] Static fixes',
                'Fixed',
                'ChangelogLinker',
                '- [#878] Static fixes',
                'Unreleased'
            ),
            new Change(
                '- [#877] [ChangelogLinker] Fixes based on feedback 2',
                'Fixed',
                'ChangelogLinker',
                '- [#877] Fixes based on feedback 2',
                'Unreleased'
            ),
            new Change(
                '- [#886] [BetterPhpDocParser] Fix annotation spacing',
                'Fixed',
                'BetterPhpDocParser',
                '- [#886] Fix annotation spacing',
                'Unreleased'
            ),
            new Change(
                '- [#881] [ChangelogLinker] Simplify ChangeFactory creating + Add tags feature supports',
                'Added',
                'ChangelogLinker',
                '- [#881] Simplify ChangeFactory creating + Add tags feature supports',
                'v3.0.0-RC2'
            ),
            new Change(
                '- [#880] Improve cognitive comlexity',
                'Changed',
                'Unknown Package',
                '- [#880] Improve cognitive comlexity',
                'Unreleased'
            ),
            new Change(
                '- [#872] Update CHANGELOG for news after 4.4',
                'Changed',
                'Unknown Package',
                '- [#872] Update CHANGELOG for news after 4.4',
                'Unreleased'
            ),
            new Change(
                '- [#887] [Statie] Improve latte decoupling from the Statie',
                'Changed',
                'Statie',
                '- [#887] Improve latte decoupling from the Statie',
                'Unreleased'
            ),
            new Change(
                '- [#884] [ChangelogLinker] Change --in-tags option to cooperate with --in-packages and --in-categories',
                'Changed',
                'ChangelogLinker',
                '- [#884] Change --in-tags option to cooperate with --in-packages and --in-categories',
                'v3.0.0-RC2'
            ),
            new Change(
                '- [#883] [ChangelogLinker] Improve --in-tags option',
                'Changed',
                'ChangelogLinker',
                '- [#883] Improve --in-tags option',
                'v3.0.0'
            ),
            new Change(
                '- [#871] [ChangelogLinker] Improve test coverage',
                'Changed',
                'ChangelogLinker',
                '- [#871] Improve test coverage',
                'Unreleased'
            ),
            new Change('- [#892] [Statie] Add Twig', 'Added', 'Statie', '- [#892] Add Twig', 'Unreleased'),
            new Change(
                "- [#900] [CodingStandard] Add 'extra_parent_types_to_suffixes' option to ClassNameSuffixByParentFixer",
                'Added',
                'CodingStandard',
                "- [#900] Add 'extra_parent_types_to_suffixes' option to ClassNameSuffixByParentFixer",
                'Unreleased'
            ),
            new Change(
                '- [#903] [ChangelogLinker] Add --linkfy option to dump-merges command',
                'Added',
                'ChangelogLinker',
                '- [#903] Add --linkfy option to dump-merges command',
                'Unreleased'
            ),
            new Change(
                '- [#902] [ChangelogLinker] Add --dry-run option to dump-merges command to dump to the output vs write into CHANGELOG.md',
                'Added',
                'ChangelogLinker',
                '- [#902] Add --dry-run option to dump-merges command to dump to the output vs write into CHANGELOG.md',
                'Unreleased'
            ),
            new Change(
                '- [#895] [Statie] Decouple CodeBlocksProtector',
                'Unknown Category',
                'Statie',
                '- [#895] Decouple CodeBlocksProtector',
                'Unreleased'
            ),
        ];
    }
}
