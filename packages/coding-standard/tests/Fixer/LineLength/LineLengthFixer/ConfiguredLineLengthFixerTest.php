<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\LineLength\LineLengthFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
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

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/FixtureConfigured');
    }

    protected function getCheckerClass(): string
    {
        return LineLengthFixer::class;
    }

    /**
     * @return mixed[]
     */
    protected function getCheckerConfiguration(): array
    {
        return [
            LineLengthFixer::LINE_LENGTH => 100,
            LineLengthFixer::BREAK_LONG_LINES => true,
            LineLengthFixer::INLINE_SHORT_LINES => false,
        ];
    }
}
