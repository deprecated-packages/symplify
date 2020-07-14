<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangelogFormatter;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangelogFormatter;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\SmartFileSystem\SmartFileInfo;

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
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        [$input, $expectedOutput] = StaticFixtureSplitter::splitFileInfoToInputAndExpected($fixtureFileInfo);

        $output = $this->changelogFormatter->format($input);
        $this->assertSame($expectedOutput, $output, $fixtureFileInfo->getRelativeFilePathFromCwd());
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Source', '*.txt');
    }
}
