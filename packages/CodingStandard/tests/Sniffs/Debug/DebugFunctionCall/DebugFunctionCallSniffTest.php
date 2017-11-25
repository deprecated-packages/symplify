<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Debug\DebugFunctionCall;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\Sniffs\Debug\DebugFunctionCallSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class DebugFunctionCallSniffTest extends AbstractSniffTestCase
{
    public function testWrong(): void
    {
        $this->doTestWrongFile(__DIR__ . '/wrong/wrong.php.inc');
    }

    public function testCorrect(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/correct/correct.php.inc');
    }

    protected function createSniff(): Sniff
    {
        return new DebugFunctionCallSniff();
    }
}
