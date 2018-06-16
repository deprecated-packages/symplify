<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Console\Output;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangeTree\Change;
use Symplify\ChangelogLinker\Console\Formatter\DumpMergesFormatter;
use Symplify\ChangelogLinker\Console\Output\DumpMergesReporter;
use Symplify\ChangelogLinker\Git\GitCommitDateTagResolver;

final class DumpMergesReporterTest extends TestCase
{
    /**
     * @var Change[]
     */
    private $changes = [];

    /**
     * @var DumpMergesReporter
     */
    private $dumpMergesReporter;

    protected function setUp(): void
    {
        $this->dumpMergesReporter = new DumpMergesReporter(new GitCommitDateTagResolver(), new DumpMergesFormatter());

        $this->changes = [new Change('[SomePackage] Message', 'Added', 'SomePackage', 'Message', 'me', 'Unreleased')];
    }

    public function testReportChanges(): void
    {
        $this->dumpMergesReporter->reportChangesWithHeadlines($this->changes, false, false, false, 'packages');

        $this->assertStringEqualsFile(
            __DIR__ . '/DumpMergesReporterSource/expected1.md',
            $this->dumpMergesReporter->getContent()
        );
    }

    /**
     * @dataProvider provideDataForReportChangesWithHeadlines()
     */
    public function testReportBothWithCategoriesPriority(
        bool $withCategories,
        bool $withPackages,
        bool $withTags,
        string $priority,
        string $expectedOutputFile
    ): void {
        $this->dumpMergesReporter->reportChangesWithHeadlines(
            $this->changes,
            $withCategories,
            $withPackages,
            $withTags,
            $priority
        );

        $this->assertStringEqualsFile($expectedOutputFile, $this->dumpMergesReporter->getContent());
    }

    public function provideDataForReportChangesWithHeadlines(): Iterator
    {
        yield [true, false, false, 'categories', __DIR__ . '/DumpMergesReporterSource/expected2.md'];
        yield [false, true, false, 'packages', __DIR__ . '/DumpMergesReporterSource/expected3.md'];
        yield [true, true, false, 'packages', __DIR__ . '/DumpMergesReporterSource/expected4.md'];
        yield [true, true, false, 'categories', __DIR__ . '/DumpMergesReporterSource/expected5.md'];
    }
}
