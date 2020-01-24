<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Strict\BlankLineAfterStrictTypesFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class BlankLineAfterStrictTypesFixerTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideDataForTestFix()
     */
    public function testFix(string $file): void
    {
        $this->doTestFiles([$file]);
    }

    public function provideDataForTestFix(): Iterator
    {
        yield [__DIR__ . '/Fixture/correct.php.inc'];
        yield [__DIR__ . '/Fixture/wrong.php.inc'];
        yield [__DIR__ . '/Fixture/wrong2.php.inc'];
    }

    protected function getCheckerClass(): string
    {
        return BlankLineAfterStrictTypesFixer::class;
    }
}
