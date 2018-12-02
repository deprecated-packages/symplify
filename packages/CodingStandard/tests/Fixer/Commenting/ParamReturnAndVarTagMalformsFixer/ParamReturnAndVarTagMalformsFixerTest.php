<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer;

use Iterator;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer
 */
final class ParamReturnAndVarTagMalformsFixerTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideWrongToFixedCases()
     */
    public function testWrongToFixedCases(string $wrongFile, string $correctFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $correctFile);
    }

    public function provideWrongToFixedCases(): Iterator
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
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
