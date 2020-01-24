<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Property\ArrayPropertyDefaultValueFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\Property\ArrayPropertyDefaultValueFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ArrayPropertyDefaultValueFixerTest extends AbstractCheckerTestCase
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
        yield [__DIR__ . '/Fixture/correct.php.inc'];
        yield [__DIR__ . '/Fixture/wrong.php.inc'];
        yield [__DIR__ . '/Fixture/wrong2.php.inc'];
        yield [__DIR__ . '/Fixture/wrong3.php.inc'];
        yield [__DIR__ . '/Fixture/wrong4.php.inc'];
    }

    protected function getCheckerClass(): string
    {
        return ArrayPropertyDefaultValueFixer::class;
    }
}
