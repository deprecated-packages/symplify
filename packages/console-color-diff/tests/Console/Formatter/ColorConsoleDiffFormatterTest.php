<?php

declare(strict_types=1);

namespace Symplify\ConsoleColorDiff\Tests\Console\Formatter;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ConsoleColorDiff\Console\Formatter\ColorConsoleDiffFormatter;

final class ColorConsoleDiffFormatterTest extends TestCase
{
    /**
     * @var ColorConsoleDiffFormatter
     */
    private $colorConsoleDiffFormatter;

    protected function setUp(): void
    {
        $this->colorConsoleDiffFormatter = new ColorConsoleDiffFormatter();
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $content, string $expectedFormatedFileContent): void
    {
        $formattedContent = $this->colorConsoleDiffFormatter->format($content);

        $this->assertStringEqualsFile($expectedFormatedFileContent, $formattedContent);
    }

    public function provideData(): Iterator
    {
        yield ['...', __DIR__ . '/Source/expected/expected.txt'];
        yield ["-old\n+new", __DIR__ . '/Source/expected/expected_old_new.txt'];
    }
}
