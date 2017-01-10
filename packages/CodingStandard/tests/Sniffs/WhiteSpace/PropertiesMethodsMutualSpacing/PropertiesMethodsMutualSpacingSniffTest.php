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

        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong.php.inc'));
        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong2.php.inc'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct.php.inc'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct2.php.inc'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct3.php.inc'));
    }

    public function testFixing()
    {
        $codeSnifferRunner = new CodeSnifferRunner(PropertiesMethodsMutualSpacingSniff::NAME);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong.php.inc');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php.inc'), $fixedContent);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong2.php.inc');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php.inc'), $fixedContent);
    }
}
