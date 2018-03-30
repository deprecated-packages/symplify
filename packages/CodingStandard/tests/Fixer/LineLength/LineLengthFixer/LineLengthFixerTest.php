<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\LineLength\LineLengthFixer;

use Iterator;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer
 */
final class LineLengthFixerTest extends AbstractCheckerTestCase
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
     * @dataProvider wrongToFixedCases()
     */
    public function test(string $wrongFile, string $fixedFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $fixedFile);
    }

    public function wrongToFixedCases(): Iterator
    {
        yield [__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc'];
        yield [__DIR__ . '/wrong/wrong2.php.inc', __DIR__ . '/fixed/fixed2.php.inc'];
        yield [__DIR__ . '/wrong/wrong3.php.inc', __DIR__ . '/fixed/fixed3.php.inc'];
        yield [__DIR__ . '/wrong/wrong4.php.inc', __DIR__ . '/fixed/fixed4.php.inc'];
        yield [__DIR__ . '/wrong/wrong5.php.inc', __DIR__ . '/fixed/fixed5.php.inc'];
        yield [__DIR__ . '/wrong/wrong6.php.inc', __DIR__ . '/fixed/fixed6.php.inc'];
        yield [__DIR__ . '/wrong/wrong7.php.inc', __DIR__ . '/fixed/fixed7.php.inc'];
        yield [__DIR__ . '/wrong/wrong8.php.inc', __DIR__ . '/fixed/fixed8.php.inc'];
        yield [__DIR__ . '/wrong/wrong9.php.inc', __DIR__ . '/fixed/fixed9.php.inc'];
        yield [__DIR__ . '/wrong/wrong10.php.inc', __DIR__ . '/fixed/fixed10.php.inc'];
        yield [__DIR__ . '/wrong/wrong11.php.inc', __DIR__ . '/fixed/fixed11.php.inc'];
        yield [__DIR__ . '/wrong/wrong12.php.inc', __DIR__ . '/fixed/fixed12.php.inc'];
        yield [__DIR__ . '/wrong/wrong13.php.inc', __DIR__ . '/fixed/fixed13.php.inc'];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
