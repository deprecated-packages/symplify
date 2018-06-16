<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Console\Formatter;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\Console\Formatter\DumpMergesFormatter;

final class DumpMergesFormatterTest extends TestCase
{
    /**
     * @var DumpMergesFormatter
     */
    private $dumpMergesFormatter;

    protected function setUp(): void
    {
        $this->dumpMergesFormatter = new DumpMergesFormatter();
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $fileToFormat, string $expectedFormattedFile): void
    {
        $fileContentToFormat = file_get_contents($fileToFormat);

        $this->assertStringEqualsFile($expectedFormattedFile, $this->dumpMergesFormatter->format($fileContentToFormat));
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/DumpMergesFormatterSource/before.txt', __DIR__ . '/DumpMergesFormatterSource/after.txt'];
        yield [__DIR__ . '/DumpMergesFormatterSource/before2.txt', __DIR__ . '/DumpMergesFormatterSource/after2.txt'];
    }
}
