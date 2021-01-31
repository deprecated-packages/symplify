<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

final class StandaloneLineInMultilineArrayFixerTest extends AbstractCheckerTestCase
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
        return StaticFixtureFinder::yieldDirectoryExclusively(__DIR__ . '/Fixture');
    }

    protected function getCheckerClass(): string
    {
        return StandaloneLineInMultilineArrayFixer::class;
    }
}
