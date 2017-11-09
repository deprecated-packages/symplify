<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Import\ImportNamespacedNameFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use Symplify\CodingStandard\Fixer\Import\ImportNamespacedNameFixer;

final class Test extends AbstractFixerTestCase
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
//            [
//                file_get_contents(__DIR__ . '/fixed/fixed.php.inc'),
//                file_get_contents(__DIR__ . '/wrong/wrong.php.inc'),
//            ],
//            [
//                file_get_contents(__DIR__ . '/fixed/fixed2.php.inc'),
//                file_get_contents(__DIR__ . '/wrong/wrong2.php.inc'),
//            ],
//            [
//                file_get_contents(__DIR__ . '/fixed/fixed3.php.inc'),
//                file_get_contents(__DIR__ . '/wrong/wrong3.php.inc'),
//            ],
//            [
//                file_get_contents(__DIR__ . '/fixed/fixed4.php.inc'),
//                file_get_contents(__DIR__ . '/wrong/wrong4.php.inc'),
//            ],
//            [
//                file_get_contents(__DIR__ . '/fixed/fixed5.php.inc'),
//                file_get_contents(__DIR__ . '/wrong/wrong5.php.inc'),
//            ],
//            [
//                file_get_contents(__DIR__ . '/fixed/fixed6.php.inc'),
//                file_get_contents(__DIR__ . '/wrong/wrong6.php.inc'),
//            ],
            [
                file_get_contents(__DIR__ . '/fixed/fixed7.php.inc'),
                file_get_contents(__DIR__ . '/wrong/wrong7.php.inc'),
            ],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        return new ImportNamespacedNameFixer;
    }
}
