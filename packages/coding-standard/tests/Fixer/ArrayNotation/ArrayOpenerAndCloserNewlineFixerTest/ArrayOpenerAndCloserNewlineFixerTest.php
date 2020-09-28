<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixerTest;

use Iterator;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ArrayOpenerAndCloserNewlineFixerTest extends AbstractCheckerTestCase
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
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture');
    }

    protected function getCheckerClass(): string
    {
        return ArrayOpenerAndCloserNewlineFixer::class;
    }
}
