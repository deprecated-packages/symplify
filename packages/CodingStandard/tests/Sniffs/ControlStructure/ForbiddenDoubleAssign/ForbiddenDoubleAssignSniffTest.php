<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\ControlStructure\ForbiddenDoubleAssign;

use Iterator;
use Symplify\CodingStandard\Sniffs\ControlStructure\ForbiddenDoubleAssignSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ForbiddenDoubleAssignSniffTest extends AbstractCheckerTestCase
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
        yield [__DIR__ . '/Fixture/wrong.php.inc'];
    }

    protected function getCheckerClass(): string
    {
        return ForbiddenDoubleAssignSniff::class;
    }
}
