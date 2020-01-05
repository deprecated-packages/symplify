<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveEmptyDocBlockFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\Commenting\RemoveEmptyDocBlockFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class RemoveEmptyDocBlockFixerTest extends AbstractCheckerTestCase
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
        yield [__DIR__ . '/Fixture/wrong2.php.inc'];
        yield [__DIR__ . '/Fixture/wrong3.php.inc'];
    }

    protected function getCheckerClass(): string
    {
        return RemoveEmptyDocBlockFixer::class;
    }
}
