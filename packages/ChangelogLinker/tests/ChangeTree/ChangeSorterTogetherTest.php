<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangeTree;

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

    public function testSortWithCategoryPriority(): void
    {
        $changes = $this->createChanges();

        $sortedChanges = $this->changeSorter->sort($changes, ChangeSorter::PRIORITY_CATEGORIES);

        $changesWithAddedCount = 0;
        foreach ($sortedChanges as $change) {
            if ($change->getCategory() === 'Added') {
                ++$changesWithAddedCount;
            }
        }

        // first X changes should have "Added" category
        for ($i = 0; $i < $changesWithAddedCount; ++$i) {
            $this->assertSame('Added', $sortedChanges[$i]->getCategory());
        }
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
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                '- [#876] [ChangelogLinker] Fixes based on feedback',
                'Fixed',
                'ChangelogLinker',
                '- [#876] Fixes based on feedback',
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                '- [#893] [Statie] Rename FlatWhite to Latte and move Latte-related code there',
                'Unknown Category',
                'Statie',
                '- [#893] Rename FlatWhite to Latte and move Latte-related code there',
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                '- [#888]  [Statie] Return collector-based approach to FileDecorators, with priorities',
                'Unknown Category',
                'Statie',
                '- [#888]  Return collector-based approach to FileDecorators, with priorities',
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                '- [#905] [ChangelogLinker] Drop commit referencing to stprevent promoting my bad practise',
                'Removed',
                'ChangelogLinker',
                '- [#905] Drop commit referencing to stprevent promoting my bad practise',
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                '- [#885] [ChangelogLinker] Drop ReleaseReferencesWorker - replaced by dump-mer…',
                'Removed',
                'ChangelogLinker',
                '- [#885] Drop ReleaseReferencesWorker - replaced by dump-mer…',
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                '- [#875] Fixes monorepo splitting by travis cron job, Thanks to @JanMikes',
                'Fixed',
                'Unknown Package',
                '- [#875] Fixes monorepo splitting by travis cron job, Thanks to @JanMikes',
                'JanMikes',
                'Unreleased'
            ),
            new Change(
                '- [#870] RemoveUselessDocBlockFixer should not reformat custom annotations, Thanks to @jankonas',
                'Fixed',
                'Unknown Package',
                '- [#870] RemoveUselessDocBlockFixer should not reformat custom annotations, Thanks to @jankonas',
                'jankonas',
                'Unreleased'
            ),
            new Change(
                '- [#901]  [CodingStandard] Allow list option in ClassNameSuffixByParentFixer',
                'Fixed',
                'CodingStandard',
                '- [#901]  Allow list option in ClassNameSuffixByParentFixer',
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                '- [#878] [ChangelogLinker] Static fixes',
                'Fixed',
                'ChangelogLinker',
                '- [#878] Static fixes',
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                '- [#877] [ChangelogLinker] Fixes based on feedback 2',
                'Fixed',
                'ChangelogLinker',
                '- [#877] Fixes based on feedback 2',
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                '- [#886] [BetterPhpDocParser] Fix annotation spacing',
                'Fixed',
                'BetterPhpDocParser',
                '- [#886] Fix annotation spacing',
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                '- [#881] [ChangelogLinker] Simplify ChangeFactory creating + Add tags feature supports',
                'Added',
                'ChangelogLinker',
                '- [#881] Simplify ChangeFactory creating + Add tags feature supports',
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                '- [#880] Improve cognitive comlexity',
                'Changed',
                'Unknown Package',
                '- [#880] Improve cognitive comlexity',
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                '- [#872] Update CHANGELOG for news after 4.4',
                'Changed',
                'Unknown Package',
                '- [#872] Update CHANGELOG for news after 4.4',
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                '- [#887] [Statie] Improve latte decoupling from the Statie',
                'Changed',
                'Statie',
                '- [#887] Improve latte decoupling from the Statie',
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                '- [#884] [ChangelogLinker] Change --in-tags option to cooperate with --in-packages and --in-categories',
                'Changed',
                'ChangelogLinker',
                '- [#884] Change --in-tags option to cooperate with --in-packages and --in-categories',
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                '- [#883] [ChangelogLinker] Improve --in-tags option',
                'Changed',
                'ChangelogLinker',
                '- [#883] Improve --in-tags option',
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                '- [#871] [ChangelogLinker] Improve test coverage',
                'Changed',
                'ChangelogLinker',
                '- [#871] Improve test coverage',
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                '- [#892] [Statie] Add Twig',
                'Added',
                'Statie',
                '- [#892] Add Twig',
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                "- [#900] [CodingStandard] Add 'extra_parent_types_to_suffixes' option to ClassNameSuffixByParentFixer",
                'Added',
                'CodingStandard',
                "- [#900] Add 'extra_parent_types_to_suffixes' option to ClassNameSuffixByParentFixer",
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                '- [#903] [ChangelogLinker] Add --linkfy option to dump-merges command',
                'Added',
                'ChangelogLinker',
                '- [#903] Add --linkfy option to dump-merges command',
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                '- [#902] [ChangelogLinker] Add --dry-run option to dump-merges command to dump to the output vs write into CHANGELOG.md',
                'Added',
                'ChangelogLinker',
                '- [#902] Add --dry-run option to dump-merges command to dump to the output vs write into CHANGELOG.md',
                'TomasVotruba',
                'Unreleased'
            ),
            new Change(
                '- [#895] [Statie] Decouple CodeBlocksProtector',
                'Unknown Category',
                'Statie',
                '- [#895] Decouple CodeBlocksProtector',
                'TomasVotruba',
                'Unreleased'
            ),
        ];
    }
}
