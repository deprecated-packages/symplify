<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Analyzer\IdsAnalyzer;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\Analyzer\IdsAnalyzer;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\SmartFileSystem\SmartFileInfo;

final class IdsAnalyzerTest extends TestCase
{
    /**
     * @var IdsAnalyzer
     */
    private $idsAnalyzer;

    protected function setUp(): void
    {
        $this->idsAnalyzer = new IdsAnalyzer();
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputAndExpected = StaticFixtureSplitter::splitFileInfoToInputAndExpected($fixtureFileInfo);

        $foundHighestId = $this->idsAnalyzer->getHighestIdInChangelog($inputAndExpected->getInput());
        $this->assertSame((int) $inputAndExpected->getExpected(), $foundHighestId);
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Source', '*.md');
    }
}
