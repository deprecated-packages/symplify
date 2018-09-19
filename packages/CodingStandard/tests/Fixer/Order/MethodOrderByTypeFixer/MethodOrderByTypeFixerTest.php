<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Order\MethodOrderByTypeFixer;

use Iterator;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Fixer\Order\MethodOrderByTypeFixer
 */
final class MethodOrderByTypeFixerTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideCorrectCases()
     */
    public function testCorrect(string $correctFile): void
    {
        $this->doTestCorrectFile($correctFile);
    }

    public function provideCorrectCases(): Iterator
    {
        yield [__DIR__ . '/Correct/AbstractClass.php.inc'];
        yield [__DIR__ . '/Correct/FixerWithAbstractParent.php'];
    }

    /**
     * @dataProvider provideWrongToFixedCases()
     */
    public function testWrongToFixed(string $wrongFile, string $fixedFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $fixedFile);
    }

    public function provideWrongToFixedCases(): Iterator
    {
        yield [__DIR__ . '/Wrong/SomeFixer.php', __DIR__ . '/fixed/fixed.php.inc'];
        yield [__DIR__ . '/Wrong/RealFixer.php', __DIR__ . '/fixed/fixed2.php.inc'];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
