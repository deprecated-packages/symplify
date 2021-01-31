<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Naming\StandardizeHereNowDocKeywordFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\Naming\StandardizeHereNowDocKeywordFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @requires PHP 7.3
 */
final class Php73Test extends AbstractCheckerTestCase
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
        return StaticFixtureFinder::yieldDirectoryExclusively(__DIR__ . '/FixturePhp73');
    }

    protected function getCheckerClass(): string
    {
        return StandardizeHereNowDocKeywordFixer::class;
    }
}
