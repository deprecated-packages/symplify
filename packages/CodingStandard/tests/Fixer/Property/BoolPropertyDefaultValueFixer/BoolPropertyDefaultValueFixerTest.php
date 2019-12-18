<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Property\BoolPropertyDefaultValueFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\Property\BoolPropertyDefaultValueFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class BoolPropertyDefaultValueFixerTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideDataForTest()
     */
    public function test(string $file): void
    {
        $this->doTestFiles([$file]);
    }

    protected function getCheckerClass(): string
    {
        return BoolPropertyDefaultValueFixer::class;
    }
    public function provideDataForTest(): Iterator
    {
        yield [__DIR__ . '/Integration/simple.php.inc'];
    }
}
