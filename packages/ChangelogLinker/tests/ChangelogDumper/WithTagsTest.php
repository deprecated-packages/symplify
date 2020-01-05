<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangelogDumper;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangelogDumper;
use Symplify\ChangelogLinker\ChangelogFormatter;
use Symplify\ChangelogLinker\ChangeTree\Change;
use Symplify\ChangelogLinker\Git\GitCommitDateTagResolver;

final class WithTagsTest extends TestCase
{
    /**
     * @var Change[]
     */
    private $changes = [];

    /**
     * @var ChangelogDumper
     */
    private $changelogDumper;

    protected function setUp(): void
    {
        $this->changelogDumper = new ChangelogDumper(new GitCommitDateTagResolver(), new ChangelogFormatter());

        $this->changes = [new Change('[SomePackage] Message', 'Added', 'SomePackage', 'Message', 'v4.0.0')];
    }

    public function testReportChanges(): void
    {
        $content = $this->changelogDumper->reportChangesWithHeadlines($this->changes, false, false, 'categories');

        if (defined('SYMPLIFY_MONOREPO')) {
            $expectedFile = __DIR__ . '/WithTagsSource/expected1.md';
        } else {
            $expectedFile = __DIR__ . '/WithTagsSource/expected1-split.md';
        }

        $this->assertStringEqualsFile($expectedFile, $content);
    }

    /**
     * @dataProvider provideDataForReportChangesWithHeadlines()
     */
    public function testReportBothWithCategoriesPriority(
        bool $withCategories,
        bool $withPackages,
        ?string $priority,
        string $expectedOutputFile
    ): void {
        $content = $this->changelogDumper->reportChangesWithHeadlines(
            $this->changes,
            $withCategories,
            $withPackages,
            $priority
        );

        $this->assertStringEqualsFile($expectedOutputFile, $content);
    }

    public function provideDataForReportChangesWithHeadlines(): Iterator
    {
        if (defined('SYMPLIFY_MONOREPO')) {
            yield [true, false, null, __DIR__ . '/WithTagsSource/expected2.md'];
            yield [false, true, null, __DIR__ . '/WithTagsSource/expected3.md'];
        } else {
            yield [true, false, null, __DIR__ . '/WithTagsSource/expected2-split.md'];
            yield [false, true, null, __DIR__ . '/WithTagsSource/expected3-split.md'];
        }
    }
}
