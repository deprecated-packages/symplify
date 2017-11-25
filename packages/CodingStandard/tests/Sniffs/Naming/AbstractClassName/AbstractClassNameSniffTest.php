<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Naming\AbstractClassName;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\Sniffs\Naming\AbstractClassNameSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class AbstractClassNameSniffTest extends AbstractSniffTestCase
{
    public function testWrongToFixed(): void
    {
        $this->doTest(__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/wrong/wrong-fixed.php.inc');
    }

    /**
     * @dataProvider provideCorrectCases()
     */
    public function testCorrect(string $file): void
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

    protected function createSniff(): Sniff
    {
        return new AbstractClassNameSniff();
    }
}
