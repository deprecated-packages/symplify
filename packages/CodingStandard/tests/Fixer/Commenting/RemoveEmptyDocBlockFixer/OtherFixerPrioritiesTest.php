<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveEmptyDocBlockFixer;

use Iterator;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class OtherFixerPrioritiesTest extends AbstractCheckerTestCase
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
        yield [__DIR__ . '/Fixture/wrong4.php.inc'];
        yield [__DIR__ . '/Fixture/wrong5.php.inc'];
        yield [__DIR__ . '/Fixture/wrong6.php.inc'];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/priorities-config.yml';
    }
}
