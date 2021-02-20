<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangelogDumper;

use Iterator;
use Symplify\ChangelogLinker\ChangelogDumper;
use Symplify\ChangelogLinker\HttpKernel\ChangelogLinkerKernel;
use Symplify\ChangelogLinker\ValueObject\ChangeTree\Change;
use Symplify\ChangelogLinker\ValueObject\PackageCategoryPriority;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class WithTagsTest extends AbstractKernelTestCase
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
        $this->bootKernel(ChangelogLinkerKernel::class);
        $this->changelogDumper = $this->getService(ChangelogDumper::class);

        $this->changes = [new Change('[SomePackage] Message', 'Added', 'SomePackage', 'Message', 'v4.0.0')];
    }

    public function testReportChanges(): void
    {
        $this->markTestSkipped('Random false positives on Github Actions');

        $content = $this->changelogDumper->reportChangesWithHeadlines($this->changes, false, false, 'categories');

        $expectedFile = __DIR__ . '/WithTagsSource/expected1.md';
        $this->assertStringEqualsFile($expectedFile, $content);
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
        $this->markTestSkipped('Random false positives on Github Actions');

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
        yield [true, false, PackageCategoryPriority::NONE, __DIR__ . '/WithTagsSource/expected2.md'];
        yield [false, true, PackageCategoryPriority::NONE, __DIR__ . '/WithTagsSource/expected3.md'];
    }
}
