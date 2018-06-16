<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Console\Output;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangeTree\Change;
use Symplify\ChangelogLinker\Console\Output\DumpMergesReporter;
use Symplify\ChangelogLinker\Git\GitCommitDateTagResolver;

final class WithTagsTest extends TestCase
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
        $this->dumpMergesReporter = new DumpMergesReporter(new GitCommitDateTagResolver());

        $this->changes = [new Change('[SomePackage] Message', 'Added', 'SomePackage', 'Message', 'me', 'v2.0.0')];
    }

    public function testReportChanges(): void
    {
        // @see https://docs.travis-ci.com/user/environment-variables/#Default-Environment-Variables
        if (getenv('TRAVIS')) {
            $this->markTestSkipped('Travis makes shallow clones, so unable to test commits/tags.');
        }

        $this->dumpMergesReporter->reportChanges($this->changes, true);

        $this->assertStringEqualsFile(
            __DIR__ . '/WithTagsSource/expected1.md',
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
        // @see https://docs.travis-ci.com/user/environment-variables/#Default-Environment-Variables
        if (getenv('TRAVIS')) {
            $this->markTestSkipped('Travis makes shallow clones, so unable to test commits/tags.');
        }

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
        yield [true, false, true, 'categories', __DIR__ . '/WithTagsSource/expected2.md'];
        yield [false, true, true, 'categories', __DIR__ . '/WithTagsSource/expected3.md'];
    }
}
