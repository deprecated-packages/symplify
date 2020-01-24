<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class StandaloneLineInMultilineArrayFixerTest extends AbstractCheckerTestCase
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
        yield [__DIR__ . '/fixture/correct.php.inc'];
        yield [__DIR__ . '/fixture/correct2.php.inc'];
        yield [__DIR__ . '/fixture/correct3.php.inc'];
        yield [__DIR__ . '/fixture/correct4.php.inc'];
        yield [__DIR__ . '/fixture/correct5.php.inc'];
        yield [__DIR__ . '/fixture/correct6.php.inc'];
        yield [__DIR__ . '/fixture/wrong.php.inc'];
        yield [__DIR__ . '/fixture/wrong2.php.inc'];
        yield [__DIR__ . '/fixture/wrong3.php.inc'];
        yield [__DIR__ . '/fixture/wrong4.php.inc'];
    }

    protected function getCheckerClass(): string
    {
        return StandaloneLineInMultilineArrayFixer::class;
    }
}
