<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Solid\FinalInterfaceFixer;

use Iterator;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Fixer\Solid\FinalInterfaceFixer
 */
final class FinalInterfaceFixerTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideCorrectCases()
     */
    public function testCorrectCases(string $file): void
    {
        $this->doTestCorrectFile($file);
    }

    public function provideCorrectCases(): Iterator
    {
        yield [__DIR__ . '/correct/correct.php.inc'];
        yield [__DIR__ . '/correct/correct2.php.inc'];
        yield [__DIR__ . '/correct/correct3.php.inc'];
        yield [__DIR__ . '/correct/correct4.php.inc'];
        yield [__DIR__ . '/correct/correct5.php.inc'];
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
        yield [__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc'];
        yield [__DIR__ . '/wrong/wrong2.php.inc', __DIR__ . '/fixed/fixed2.php.inc'];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
