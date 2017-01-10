<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Classes\ClassDeclaration;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Tests\CodeSnifferRunner;
use SymplifyCodingStandard\Sniffs\Classes\ClassDeclarationSniff;

final class ClassDeclarationSniffTest extends TestCase
{
    public function testDetection()
    {
        $codeSnifferRunner = new CodeSnifferRunner(ClassDeclarationSniff::NAME);

        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong.php.inc'));
        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong2.php.inc'));
        $this->assertSame(2, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong3.php.inc'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct.php.inc'));
    }

    public function testFixing()
    {
        $codeSnifferRunner = new CodeSnifferRunner(ClassDeclarationSniff::NAME);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong.php.inc');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php.inc'), $fixedContent);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong2.php.inc');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php.inc'), $fixedContent);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong3.php.inc');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php.inc'), $fixedContent);
    }
}
