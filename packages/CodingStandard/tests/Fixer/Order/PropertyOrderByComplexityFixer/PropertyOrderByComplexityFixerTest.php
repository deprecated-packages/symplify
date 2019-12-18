<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Order\PropertyOrderByComplexityFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\Order\PropertyOrderByComplexityFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class PropertyOrderByComplexityFixerTest extends AbstractCheckerTestCase
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
        return PropertyOrderByComplexityFixer::class;
    }
    public function provideDataForTest(): Iterator
    {
        yield [__DIR__ . '/Fixture/wrong.php.inc'];
        yield [__DIR__ . '/Fixture/wrong2.php.inc'];
        yield [__DIR__ . '/Fixture/correct.php.inc'];
    }
}
