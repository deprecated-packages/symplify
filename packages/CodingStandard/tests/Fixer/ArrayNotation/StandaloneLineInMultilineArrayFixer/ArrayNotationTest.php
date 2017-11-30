<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class ArrayNotationTest extends AbstractSimpleFixerTestCase
{
    /**
     * @dataProvider provideCorrectCases()
     */
    public function testCorrectCases(string $correctFile): void
    {
        $this->doTestCorrectFile($correctFile);
    }

    /**
     * @return string[][]
     */
    public function provideCorrectCases(): array
    {
        return [
            [__DIR__ . '/correct/correct.php.inc'],
            [__DIR__ . '/correct/correct2.php.inc'],
            [__DIR__ . '/correct/correct3.php.inc'],
            [__DIR__ . '/correct/correct4.php.inc'],
            [__DIR__ . '/correct/correct5.php.inc'],
            [__DIR__ . '/correct/correct6.php.inc'],
        ];
    }

    /**
     * @dataProvider provideWrongToFixedCases()
     */
    public function testWrongToFixedCases(string $wrongFile, string $correctFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $correctFile);
    }

    /**
     * @return string[][]
     */
    public function provideWrongToFixedCases(): array
    {
        return [
            [__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc'],
            [__DIR__ . '/wrong/wrong2.php.inc', __DIR__ . '/fixed/fixed2.php.inc'],
            [__DIR__ . '/wrong/wrong3.php.inc', __DIR__ . '/fixed/fixed3.php.inc'],
            [__DIR__ . '/wrong/wrong4.php.inc', __DIR__ . '/fixed/fixed4.php.inc'],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        $fixer = new StandaloneLineInMultilineArrayFixer();
        $fixer->setWhitespacesConfig(new WhitespacesFixerConfig('    ', PHP_EOL));

        return $fixer;
    }
}
