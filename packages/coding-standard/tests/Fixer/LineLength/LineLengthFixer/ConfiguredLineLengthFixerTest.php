<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\LineLength\LineLengthFixer;

use Iterator;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ConfiguredLineLengthFixerTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);
    }

    /**
     * @return Iterator<mixed, SmartFileInfo>
     */
    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectoryExclusively(__DIR__ . '/FixtureConfigured');
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/custom_rule.php';
    }
}
