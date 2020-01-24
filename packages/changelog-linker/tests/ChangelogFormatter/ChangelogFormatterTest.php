<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangelogFormatter;

use Iterator;
use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangelogFormatter;

final class ChangelogFormatterTest extends TestCase
{
    /**
     * @var ChangelogFormatter
     */
    private $changelogFormatter;

    protected function setUp(): void
    {
        $this->changelogFormatter = new ChangelogFormatter();
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $fileToFormat, string $expectedFormattedFile): void
    {
        $fileContentToFormat = FileSystem::read($fileToFormat);

        $this->assertStringEqualsFile($expectedFormattedFile, $this->changelogFormatter->format($fileContentToFormat));
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/ChangelogFormatterSource/before.txt', __DIR__ . '/ChangelogFormatterSource/after.txt'];
        yield [__DIR__ . '/ChangelogFormatterSource/before2.txt', __DIR__ . '/ChangelogFormatterSource/after2.txt'];
    }
}
