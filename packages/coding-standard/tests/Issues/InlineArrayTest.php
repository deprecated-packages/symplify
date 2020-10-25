<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Issues;

use Iterator;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class InlineArrayTest extends AbstractCheckerTestCase
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
        yield [new SmartFileInfo(__DIR__ . '/Fixture/inline_array.php.inc')];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config/config_inline_long_array.php';
    }
}
