<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\SpaceAfterCommaHereNowDocFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\Spacing\SpaceAfterCommaHereNowDocFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @requires PHP 7.3
 */
final class SpaceAfterCommaHereNowDocFixerTest extends AbstractCheckerTestCase
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
        return SpaceAfterCommaHereNowDocFixer::class;
    }
}
