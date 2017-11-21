<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Solid\FinalInterfaceFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use Symplify\CodingStandard\Fixer\Solid\FinalInterfaceFixer;

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
            [
                file_get_contents(__DIR__ . '/fixed/fixed.php.inc'),
                file_get_contents(__DIR__ . '/wrong/wrong.php.inc'),
            ],
            [
                file_get_contents(__DIR__ . '/fixed/fixed2.php.inc'),
                file_get_contents(__DIR__ . '/wrong/wrong2.php.inc'),
            ],
            [file_get_contents(__DIR__ . '/correct/correct.php.inc')],
            [file_get_contents(__DIR__ . '/correct/correct2.php.inc')],
            [file_get_contents(__DIR__ . '/correct/correct3.php.inc')],
            [file_get_contents(__DIR__ . '/correct/correct4.php.inc')],
            [file_get_contents(__DIR__ . '/correct/correct5.php.inc')],
        ];
    }

    protected function createFixer(): FixerInterface
    {
        return new FinalInterfaceFixer();
    }
}
