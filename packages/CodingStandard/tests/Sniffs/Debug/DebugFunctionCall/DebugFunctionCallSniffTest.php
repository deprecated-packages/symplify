<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Debug\DebugFunctionCall;

use Iterator;
use Symplify\CodingStandard\Sniffs\Debug\DebugFunctionCallSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class DebugFunctionCallSniffTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideDataForTest()
     */
    public function test(string $file): void
    {
        $this->doTestFiles([$file]);
    }

    public function provideDataForTest(): Iterator
    {
        yield [__DIR__ . '/Fixture/wrong.php.inc'];
        yield [__DIR__ . '/Fixture/correct.php.inc'];
    }

    protected function getCheckerClass(): string
    {
        return DebugFunctionCallSniff::class;
    }
}
