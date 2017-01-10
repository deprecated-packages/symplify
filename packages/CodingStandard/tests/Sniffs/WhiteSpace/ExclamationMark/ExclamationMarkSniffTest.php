<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\WhiteSpace\ExclamationMark;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Tests\CodeSnifferRunner;
use SymplifyCodingStandard\Sniffs\WhiteSpace\ExclamationMarkSniff;

final class ExclamationMarkSniffTest extends TestCase
{
    public function testDetection()
    {
        $codeSnifferRunner = new CodeSnifferRunner(ExclamationMarkSniff::NAME);

        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong.php'));
        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong2.php'));
        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong3.php'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct.php'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct2.php'));
    }

    public function testFixing()
    {
        $codeSnifferRunner = new CodeSnifferRunner(ExclamationMarkSniff::NAME);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong.php');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php'), $fixedContent);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong2.php');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php'), $fixedContent);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong3.php');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php'), $fixedContent);
    }
}
