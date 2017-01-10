<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\WhiteSpace\PropertiesMethodsMutualSpacing;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Tests\CodeSnifferRunner;
use SymplifyCodingStandard\Sniffs\WhiteSpace\PropertiesMethodsMutualSpacingSniff;

final class PropertiesMethodsMutualSpacingSniffTest extends TestCase
{

    public function testDetection()
    {
        $codeSnifferRunner = new CodeSnifferRunner(PropertiesMethodsMutualSpacingSniff::NAME);

        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong.php'));
        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong2.php'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct.php'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct2.php'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct3.php'));
    }


    public function testFixing()
    {
        $codeSnifferRunner = new CodeSnifferRunner(PropertiesMethodsMutualSpacingSniff::NAME);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong.php');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php'), $fixedContent);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong2.php');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php'), $fixedContent);
    }
}
