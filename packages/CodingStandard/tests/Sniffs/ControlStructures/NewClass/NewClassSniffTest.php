<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\ControlStructures\NewClass;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Tests\CodeSnifferRunner;
use SymplifyCodingStandard\Sniffs\ControlStructures\NewClassSniff;

final class NewClassSniffTest extends TestCase
{
    public function testDetection()
    {
        $codeSnifferRunner = new CodeSnifferRunner(NewClassSniff::NAME);

        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong.php.inc'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct.php.inc'));
    }

    public function testFixing()
    {
        $codeSnifferRunner = new CodeSnifferRunner(NewClassSniff::NAME);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong.php.inc');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php.inc'), $fixedContent);
    }
}
