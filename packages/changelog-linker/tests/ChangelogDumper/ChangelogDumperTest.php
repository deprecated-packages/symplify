<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangelogDumper;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangelogDumper;
use Symplify\ChangelogLinker\ChangelogFormatter;
use Symplify\ChangelogLinker\Git\GitCommitDateTagResolver;
use Symplify\ChangelogLinker\ValueObject\ChangelogFormat;
use Symplify\ChangelogLinker\ValueObject\ChangeTree\Change;

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

    /**
     * @dataProvider provideData()
     */
    public function test(string $changelogFormat, string $expectedOutputFile): void
    {
        $content = $this->changelogDumper->reportChangesWithHeadlines($this->changes, $changelogFormat);
        $this->assertStringEqualsFile($expectedOutputFile, $content);
    }

    public function provideData(): Iterator
    {
        yield [ChangelogFormat::BARE, __DIR__ . '/ChangelogDumperSource/expected1.md'];
        yield [ChangelogFormat::CATEGORIES_ONLY, __DIR__ . '/ChangelogDumperSource/expected2.md'];
        yield [ChangelogFormat::PACKAGES_ONLY, __DIR__ . '/ChangelogDumperSource/expected3.md'];
        yield [ChangelogFormat::PACKAGES_THEN_CATEGORIES, __DIR__ . '/ChangelogDumperSource/expected4.md'];
        yield [ChangelogFormat::CATEGORIES_THEN_PACKAGES, __DIR__ . '/ChangelogDumperSource/expected5.md'];
    }
}
