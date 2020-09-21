<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Naming\StandardizeHereNowDocKeywordFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\Naming\StandardizeHereNowDocKeywordFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

final class StandardizeHereNowDocKeywordFixerTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);
    }

    public function provideData(): Iterator
    {
        $yieldDirectory = StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture');
        if (version_compare(PHP_VERSION, '7.2', '<=')) {
            $yieldDirectory = array_merge($yieldDirectory, StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture73plus'));
        }

        return $yieldDirectory;
    }

    protected function getCheckerClass(): string
    {
        return StandardizeHereNowDocKeywordFixer::class;
    }
}
