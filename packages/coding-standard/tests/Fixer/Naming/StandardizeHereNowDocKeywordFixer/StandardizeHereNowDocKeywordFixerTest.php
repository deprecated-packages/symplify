<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Naming\StandardizeHereNowDocKeywordFixer;

use AppendIterator;
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

    public function provideData(): AppendIterator
    {
        $appendIterator = new AppendIterator();
        $appendIterator->append(StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture'));
        if (version_compare(PHP_VERSION, '7.3', '>=')) {
            $appendIterator->append(StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture73plus'));
        }

        return $appendIterator;
    }

    protected function getCheckerClass(): string
    {
        return StandardizeHereNowDocKeywordFixer::class;
    }
}
