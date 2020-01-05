<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Architecture\DuplicatedClassShortNameSniff;

use Iterator;
use Symplify\CodingStandard\Sniffs\Architecture\DuplicatedClassShortNameSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class DuplicatedClassShortNameSniffTest extends AbstractCheckerTestCase
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
    }

    protected function getCheckerClass(): string
    {
        return DuplicatedClassShortNameSniff::class;
    }
}
