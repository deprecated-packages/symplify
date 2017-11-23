<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveUselessDocBlockFixer;

use PhpCsFixer\Fixer\FixerInterface;
use Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDocBlockFixer;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

final class Test extends AbstractSimpleFixerTestCase
{
    /**
     * @dataProvider provideFixCases()
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return string[][]
     */
    public function provideFixCases(): array
    {
        return [
            // correct
            [__DIR__ . '/correct/correct.php.inc'],
            [__DIR__ . '/correct/correct2.php.inc'],
            [__DIR__ . '/correct/correct3.php.inc'],
            [__DIR__ . '/correct/correct4.php.inc'],
            [__DIR__ . '/correct/correct5.php.inc'],
            // wrong => fixed
            [__DIR__ . '/wrong/wrong.php.inc' => __DIR__ . '/fixed/fixed.php.inc'],
            [__DIR__ . '/wrong/wrong2.php.inc' => __DIR__ . '/fixed/fixed2.php.inc'],
            [__DIR__ . '/wrong/wrong3.php.inc' => __DIR__ . '/fixed/fixed3.php.inc'],
            [__DIR__ . '/wrong/wrong4.php.inc' => __DIR__ . '/fixed/fixed4.php.inc'],
            [__DIR__ . '/wrong/wrong5.php.inc' => __DIR__ . '/fixed/fixed5.php.inc'],
            [__DIR__ . '/wrong/wrong6.php.inc' => __DIR__ . '/fixed/fixed6.php.inc'],
            [__DIR__ . '/wrong/wrong7.php.inc' => __DIR__ . '/fixed/fixed7.php.inc'],
            [__DIR__ . '/wrong/wrong8.php.inc' => __DIR__ . '/fixed/fixed8.php.inc'],
            [__DIR__ . '/wrong/wrong9.php.inc' => __DIR__ . '/fixed/fixed9.php.inc'],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        return new RemoveUselessDocBlockFixer();
    }
}
