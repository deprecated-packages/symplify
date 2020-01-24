<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\ControlStructure\SprintfOverContact;

use Iterator;
use Symplify\CodingStandard\Sniffs\ControlStructure\SprintfOverContactSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class SprintfOverContactSniffTest extends AbstractCheckerTestCase
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
        yield [__DIR__ . '/Fixture/correct.php.inc'];
    }

    protected function getCheckerClass(): string
    {
        return SprintfOverContactSniff::class;
    }
}
