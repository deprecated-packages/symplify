<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\CleanCode\CognitiveComplexitySniff;

use Iterator;
use Symplify\CodingStandard\Sniffs\CleanCode\CognitiveComplexitySniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class CognitiveComplexitySniffTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideDataForTest()
     */
    public function test(string $file): void
    {
        $this->doTestFiles([$file]); // #9
    }

    public function provideDataForTest(): Iterator
    {
        yield [__DIR__ . '/Fixture/wrong.php.inc'];
    }

    protected function getCheckerClass(): string
    {
        return CognitiveComplexitySniff::class;
    }
}
