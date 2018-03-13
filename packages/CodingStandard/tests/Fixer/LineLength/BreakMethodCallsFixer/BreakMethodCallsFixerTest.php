<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\LineLength\BreakMethodCallsFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\Fixer\LineLength\BreakMethodCallsFixer;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class BreakMethodCallsFixerTest extends AbstractSimpleFixerTestCase
{
    /**
     * @dataProvider provideCorrectCases()
     */
    public function testCorrectCases(string $file): void
    {
        $this->doTestCorrectFile($file);
    }

    /**
     * @return string[][]
     */
    public function provideCorrectCases(): array
    {
        return [
            [__DIR__ . '/correct/correct.php.inc'],
            [__DIR__ . '/correct/correct2.php.inc'],
        ];
    }

    /**
     * @dataProvider wrongToFixedCases()
     */
    public function testWrongToFixedCases(string $wrongFile, string $fixedFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $fixedFile);
    }

    /**
     * @return string[][]
     */
    public function wrongToFixedCases(): array
    {
        return [
            [__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc'],
            [__DIR__ . '/wrong/wrong2.php.inc', __DIR__ . '/fixed/fixed2.php.inc'],
            [__DIR__ . '/wrong/wrong3.php.inc', __DIR__ . '/fixed/fixed3.php.inc'],
            [__DIR__ . '/wrong/wrong4.php.inc', __DIR__ . '/fixed/fixed4.php.inc'],
            [__DIR__ . '/wrong/wrong5.php.inc', __DIR__ . '/fixed/fixed5.php.inc'],
            [__DIR__ . '/wrong/wrong6.php.inc', __DIR__ . '/fixed/fixed6.php.inc'],
            [__DIR__ . '/wrong/wrong7.php.inc', __DIR__ . '/fixed/fixed7.php.inc'],
            [__DIR__ . '/wrong/wrong8.php.inc', __DIR__ . '/fixed/fixed8.php.inc'],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        $fixer = new BreakMethodCallsFixer();
        $fixer->setWhitespacesConfig(new WhitespacesFixerConfig());

        return $fixer;
    }
}
