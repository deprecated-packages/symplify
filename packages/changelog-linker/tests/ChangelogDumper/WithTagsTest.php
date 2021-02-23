<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangelogDumper;

use Iterator;
use Symplify\ChangelogLinker\ChangelogDumper;
use Symplify\ChangelogLinker\HttpKernel\ChangelogLinkerKernel;
use Symplify\ChangelogLinker\ValueObject\ChangelogFormat;
use Symplify\ChangelogLinker\ValueObject\ChangeTree\Change;
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

        $content = $this->changelogDumper->reportChangesWithHeadlines($this->changes, ChangelogFormat::BARE);

        $expectedFile = __DIR__ . '/WithTagsSource/expected1.md';
        $this->assertStringEqualsFile($expectedFile, $content);
    }

    /**
     * @dataProvider provideDataForReportChangesWithHeadlines()
     */
    public function testReportBothWithCategoriesPriority(
        string $changelogFormat,
        string $expectedOutputFile
    ): void {
        $this->markTestSkipped('Random false positives on Github Actions');

        $content = $this->changelogDumper->reportChangesWithHeadlines($this->changes, $changelogFormat);

        $this->assertStringEqualsFile($expectedOutputFile, $content);
    }

    public function provideDataForReportChangesWithHeadlines(): Iterator
    {
        yield [ChangelogFormat::CATEGORIES_ONLY, __DIR__ . '/WithTagsSource/expected2.md'];
        yield [ChangelogFormat::PACKAGES_ONLY, __DIR__ . '/WithTagsSource/expected3.md'];
    }
}
