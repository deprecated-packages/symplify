<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Solid\FinalInterfaceFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\Solid\FinalInterfaceFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class FinalInterfaceFixerTest extends AbstractCheckerTestCase
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
        yield [__DIR__ . '/Fixture/correct2.php.inc'];
        yield [__DIR__ . '/Fixture/correct3.php.inc'];
        yield [__DIR__ . '/Fixture/correct4.php.inc'];
        yield [__DIR__ . '/Fixture/correct5.php.inc'];
        yield [__DIR__ . '/Fixture/correct6.php.inc'];
        yield [__DIR__ . '/Fixture/wrong.php.inc'];
        yield [__DIR__ . '/Fixture/wrong2.php.inc'];
        yield [__DIR__ . '/Fixture/wrong4.php.inc'];
    }

    protected function getCheckerClass(): string
    {
        return FinalInterfaceFixer::class;
    }
}
