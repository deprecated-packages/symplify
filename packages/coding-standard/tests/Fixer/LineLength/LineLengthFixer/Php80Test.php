<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\LineLength\LineLengthFixer;

use Iterator;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @requires PHP 8.0
 */
final class Php80Test extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideDataForTest()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);
    }

    /**
     * @return Iterator<SmartFileInfo>
     */
    public function provideDataForTest(): Iterator
    {
        return StaticFixtureFinder::yieldDirectoryExclusively(__DIR__ . '/FixturePhp80');
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }
}
