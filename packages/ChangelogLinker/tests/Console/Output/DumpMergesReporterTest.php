<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Console\Output;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ChangelogLinker\ChangeTree\Change;
use Symplify\ChangelogLinker\Console\Output\DumpMergesReporter;
use Symplify\ChangelogLinker\Git\GitCommitDateTagResolver;

final class DumpMergesReporterTest extends TestCase
{
    /**
     * @var Change[]
     */
    private $changes = [];

    /**
     * @var BufferedOutput
     */
    private $bufferedOutput;

    /**
     * @var DumpMergesReporter
     */
    private $dumpMergesReporter;

    protected function setUp(): void
    {
        $this->bufferedOutput = new BufferedOutput();
        $this->dumpMergesReporter = new DumpMergesReporter(new SymfonyStyle(
            new ArrayInput([]),
            $this->bufferedOutput
        ), new GitCommitDateTagResolver());

        $this->changes = [new Change('[SomePackage] Message', 'Added', 'SomePackage', 'Message', 'me', 'Unreleased')];
    }

    public function testReportChanges(): void
    {
        $this->dumpMergesReporter->reportChanges($this->changes, false);

        $this->assertStringEqualsFile(
            __DIR__ . '/DumpMergesReporterSource/expected1.md',
            $this->bufferedOutput->fetch()
        );

        $this->dumpMergesReporter->reportChanges($this->changes, true);

        $this->assertStringEqualsFile(
            __DIR__ . '/DumpMergesReporterSource/expected6.md',
            $this->bufferedOutput->fetch()
        );
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
        $this->dumpMergesReporter->reportChangesWithHeadlines(
            $this->changes,
            $withCategories,
            $withPackages,
            $priority
        );

        $this->assertStringEqualsFile($expectedOutputFile, $this->bufferedOutput->fetch());
    }

    public function provideDataForReportChangesWithHeadlines(): Iterator
    {
        yield [true, false, 'categories', __DIR__ . '/DumpMergesReporterSource/expected2.md'];
        yield [false, true, 'packages', __DIR__ . '/DumpMergesReporterSource/expected3.md'];
        yield [true, true, 'packages', __DIR__ . '/DumpMergesReporterSource/expected4.md'];
        yield [true, true, 'categories', __DIR__ . '/DumpMergesReporterSource/expected5.md'];
    }
}
