<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangeTree;

use Symplify\ChangelogLinker\ValueObject\ChangeTree\Change;

final class DummyChangesFactory
{
    /**
     * @var string
     */
    private const ADDED = 'Added';

    /**
     * @var string
     */
    private const CHANGELOG_LINKER = 'ChangelogLinker';

    /**
     * @var string
     */
    private const FIXED = 'Fixed';

    /**
     * @var string
     */
    private const UNRELEASED = 'Unreleased';

    /**
     * @var string
     */
    private const STATIE = 'Statie';

    /**
     * @var string
     */
    private const CHANGED = 'Changed';

    /**
     * Real life example
     *
     * @return Change[]
     */
    public function create(): array
    {
        return [
            new Change(
                '- [#879] [ChangelogLinker] Add --token option to increase Github API rate [closes #874]',
                self::ADDED,
                self::CHANGELOG_LINKER,
                '- [#879] Add --token option to increase Github API rate [closes #874]',
                'v3.0.0'
            ),
            new Change(
                '- [#876] [ChangelogLinker] Fixes based on feedback',
                self::FIXED,
                self::CHANGELOG_LINKER,
                '- [#876] Fixes based on feedback',
                self::UNRELEASED
            ),
            new Change(
                '- [#893] [Statie] Rename FlatWhite to Latte and move Latte-related code there',
                'Unknown Category',
                self::STATIE,
                '- [#893] Rename FlatWhite to Latte and move Latte-related code there',
                'v2.0'
            ),
            new Change(
                '- [#888]  [Statie] Return collector-based approach to FileDecorators, with priorities',
                'Unknown Category',
                self::STATIE,
                '- [#888]  Return collector-based approach to FileDecorators, with priorities',
                'v2.0'
            ),
            new Change(
                '- [#905] [ChangelogLinker] Drop commit referencing to stprevent promoting my bad practise',
                'Removed',
                self::CHANGELOG_LINKER,
                '- [#905] Drop commit referencing to stprevent promoting my bad practise',
                self::UNRELEASED
            ),
            new Change(
                '- [#885] [ChangelogLinker] Drop ReleaseReferencesWorker - replaced by dump-mer…',
                'Removed',
                self::CHANGELOG_LINKER,
                '- [#885] Drop ReleaseReferencesWorker - replaced by dump-mer…',
                self::UNRELEASED
            ),
            new Change(
                '- [#875] Fixes monorepo splitting by travis cron job, Thanks to @JanMikes',
                self::FIXED,
                'Unknown Package',
                '- [#875] Fixes monorepo splitting by travis cron job, Thanks to @JanMikes',
                self::UNRELEASED
            ),
            new Change(
                '- [#870] RemoveUselessDocBlockFixer should not reformat custom annotations, Thanks to @jankonas',
                self::FIXED,
                'Unknown Package',
                '- [#870] RemoveUselessDocBlockFixer should not reformat custom annotations, Thanks to @jankonas',
                self::UNRELEASED
            ),
            new Change(
                '- [#901]  [CodingStandard] Allow list option in ClassNameSuffixByParentFixer',
                self::FIXED,
                'CodingStandard',
                '- [#901]  Allow list option in ClassNameSuffixByParentFixer',
                self::UNRELEASED
            ),
            new Change(
                '- [#878] [ChangelogLinker] Static fixes',
                self::FIXED,
                self::CHANGELOG_LINKER,
                '- [#878] Static fixes',
                self::UNRELEASED
            ),
            new Change(
                '- [#877] [ChangelogLinker] Fixes based on feedback 2',
                self::FIXED,
                self::CHANGELOG_LINKER,
                '- [#877] Fixes based on feedback 2',
                self::UNRELEASED
            ),
            new Change(
                '- [#886] [BetterPhpDocParser] Fix annotation spacing',
                self::FIXED,
                'BetterPhpDocParser',
                '- [#886] Fix annotation spacing',
                self::UNRELEASED
            ),
            new Change(
                '- [#881] [ChangelogLinker] Simplify ChangeFactory creating + Add tags feature supports',
                self::ADDED,
                self::CHANGELOG_LINKER,
                '- [#881] Simplify ChangeFactory creating + Add tags feature supports',
                'v3.0.0-RC2'
            ),
            new Change(
                '- [#880] Improve cognitive comlexity',
                self::CHANGED,
                'Unknown Package',
                '- [#880] Improve cognitive comlexity',
                self::UNRELEASED
            ),
            new Change(
                '- [#872] Update CHANGELOG for news after 4.4',
                self::CHANGED,
                'Unknown Package',
                '- [#872] Update CHANGELOG for news after 4.4',
                self::UNRELEASED
            ),
            new Change(
                '- [#887] [Statie] Improve latte decoupling from the Statie',
                self::CHANGED,
                self::STATIE,
                '- [#887] Improve latte decoupling from the Statie',
                self::UNRELEASED
            ),
            new Change(
                '- [#884] [ChangelogLinker] Change --in-tags option to cooperate with --in-packages and --in-categories',
                self::CHANGED,
                self::CHANGELOG_LINKER,
                '- [#884] Change --in-tags option to cooperate with --in-packages and --in-categories',
                'v3.0.0-RC2'
            ),
            new Change(
                '- [#883] [ChangelogLinker] Improve --in-tags option',
                self::CHANGED,
                self::CHANGELOG_LINKER,
                '- [#883] Improve --in-tags option',
                'v3.0.0'
            ),
            new Change(
                '- [#871] [ChangelogLinker] Improve test coverage',
                self::CHANGED,
                self::CHANGELOG_LINKER,
                '- [#871] Improve test coverage',
                self::UNRELEASED
            ),
            new Change('- [#892] [Statie] Add Twig', self::ADDED, self::STATIE, '- [#892] Add Twig', self::UNRELEASED),
            new Change(
                "- [#900] [CodingStandard] Add 'extra_parent_types_to_suffixes' option to ClassNameSuffixByParentFixer",
                self::ADDED,
                'CodingStandard',
                "- [#900] Add 'extra_parent_types_to_suffixes' option to ClassNameSuffixByParentFixer",
                self::UNRELEASED
            ),
            new Change(
                '- [#903] [ChangelogLinker] Add --linkfy option to dump-merges command',
                self::ADDED,
                self::CHANGELOG_LINKER,
                '- [#903] Add --linkfy option to dump-merges command',
                self::UNRELEASED
            ),
            new Change(
                '- [#902] [ChangelogLinker] Add --dry-run option to dump-merges command to dump to the output vs write into CHANGELOG.md',
                self::ADDED,
                self::CHANGELOG_LINKER,
                '- [#902] Add --dry-run option to dump-merges command to dump to the output vs write into CHANGELOG.md',
                self::UNRELEASED
            ),
            new Change(
                '- [#895] [Statie] Decouple CodeBlocksProtector',
                'Unknown Category',
                self::STATIE,
                '- [#895] Decouple CodeBlocksProtector',
                self::UNRELEASED
            ),
        ];
    }
}
