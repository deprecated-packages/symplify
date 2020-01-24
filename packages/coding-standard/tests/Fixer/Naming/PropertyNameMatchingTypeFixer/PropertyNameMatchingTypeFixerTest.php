<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Naming\PropertyNameMatchingTypeFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\Naming\PropertyNameMatchingTypeFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class PropertyNameMatchingTypeFixerTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(string $file): void
    {
        $this->doTestFiles([$file]);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/correct.php.inc'];
        yield [__DIR__ . '/Fixture/correct2.php.inc'];
        yield [__DIR__ . '/Fixture/correct3.php.inc'];
        yield [__DIR__ . '/Fixture/correct4.php.inc'];
        yield [__DIR__ . '/Fixture/correct5.php.inc'];
        yield [__DIR__ . '/Fixture/correct6.php.inc'];
        yield [__DIR__ . '/Fixture/correct7.php.inc'];
        yield [__DIR__ . '/Fixture/correct8.php.inc'];
        yield [__DIR__ . '/Fixture/wrong.php.inc'];
        yield [__DIR__ . '/Fixture/wrong2.php.inc'];
        yield [__DIR__ . '/Fixture/wrong3.php.inc'];
        yield [__DIR__ . '/Fixture/wrong4.php.inc'];
        yield [__DIR__ . '/Fixture/wrong5.php.inc'];
        yield [__DIR__ . '/Fixture/wrong6.php.inc'];
        yield [__DIR__ . '/Fixture/wrong7.php.inc'];
    }

    protected function getCheckerClass(): string
    {
        return PropertyNameMatchingTypeFixer::class;
    }
}
