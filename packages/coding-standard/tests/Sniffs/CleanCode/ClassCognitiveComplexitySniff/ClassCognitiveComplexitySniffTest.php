<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\CleanCode\ClassCognitiveComplexitySniff;

use Iterator;
use Symplify\CodingStandard\Sniffs\CleanCode\ClassCognitiveComplexitySniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ClassCognitiveComplexitySniffTest extends AbstractCheckerTestCase
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
        return ClassCognitiveComplexitySniff::class;
    }
}
