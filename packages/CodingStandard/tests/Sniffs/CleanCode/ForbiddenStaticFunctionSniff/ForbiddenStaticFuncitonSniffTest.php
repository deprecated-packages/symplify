<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\CleanCode\ForbiddenStaticFunctionSniff;

use Iterator;
use Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenStaticFunctionSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ForbiddenStaticFuncitonSniffTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideWrongCases()
     */
    public function test(string $file): void
    {
        $this->doTestFiles([$file]);
    }

    public function provideWrongCases(): Iterator
    {
        yield [__DIR__ . '/wrong/wrong.php.inc'];
    }

    protected function getCheckerClass(): string
    {
        return ForbiddenStaticFunctionSniff::class;
    }
}
