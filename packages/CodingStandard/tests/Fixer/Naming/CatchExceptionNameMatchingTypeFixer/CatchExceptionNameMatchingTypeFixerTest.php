<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Naming\CatchExceptionNameMatchingTypeFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\Naming\CatchExceptionNameMatchingTypeFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class CatchExceptionNameMatchingTypeFixerTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideDataForTest()
     */
    public function test(string $file): void
    {
        $this->doTestFiles([$file]);
    }

    protected function getCheckerClass(): string
    {
        return CatchExceptionNameMatchingTypeFixer::class;
    }
    public function provideDataForTest(): Iterator
    {
        yield [__DIR__ . '/Fixture/correct.php.inc'];
        yield [__DIR__ . '/Fixture/wrong_to_fixed.php.inc'];
    }
}
