<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Debug\DebugFunctionCall;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Sniffs\Debug\DebugFunctionCallSniff
 */
final class DebugFunctionCallSniffTest extends AbstractCheckerTestCase
{
    public function testWrong(): void
    {
        $this->doTestWrongFile(__DIR__ . '/wrong/wrong.php.inc');
    }

    public function testCorrect(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/correct/correct.php.inc');
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
