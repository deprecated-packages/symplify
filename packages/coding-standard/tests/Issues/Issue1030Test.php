<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Issues;

use Iterator;
use Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenStaticFunctionSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class Issue1030Test extends AbstractCheckerTestCase
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
        yield [__DIR__ . '/Fixture/correct1030.php.inc'];
    }

    protected function getCheckerClass(): string
    {
        return ForbiddenStaticFunctionSniff::class;
    }
}
