<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangelogDumper;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangelogDumper;
use Symplify\ChangelogLinker\ChangelogFormatter;
use Symplify\ChangelogLinker\ChangeTree\Change;
use Symplify\ChangelogLinker\Git\GitCommitDateTagResolver;

final class ChangelogDumperTest extends TestCase
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

        $this->changes = [new Change('[SomePackage] Message', 'Added', 'SomePackage', 'Message', 'Unreleased')];
    }

    public function testReportChanges(): void
    {
        $content = $this->changelogDumper->reportChangesWithHeadlines($this->changes, false, false, 'packages');

        $this->assertStringEqualsFile(__DIR__ . '/ChangelogDumperSource/expected1.md', $content);
    }

    /**
     * @dataProvider provideDataForReportChangesWithHeadlines()
     */
    public function testReportBothWithCategoriesPriority(
        bool $withCategories,
        bool $withPackages,
        string $priority,
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
        yield [true, false, 'categories', __DIR__ . '/ChangelogDumperSource/expected2.md'];
        yield [false, true, 'packages', __DIR__ . '/ChangelogDumperSource/expected3.md'];
        yield [true, true, 'packages', __DIR__ . '/ChangelogDumperSource/expected4.md'];
        yield [true, true, 'categories', __DIR__ . '/ChangelogDumperSource/expected5.md'];
    }
}
