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

        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong.php'));
        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong2.php'));
        $this->assertSame(2, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong3.php'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct.php'));
    }


    public function testFixing()
    {
        $codeSnifferRunner = new CodeSnifferRunner(ClassDeclarationSniff::NAME);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong.php');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php'), $fixedContent);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong2.php');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php'), $fixedContent);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong3.php');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php'), $fixedContent);
    }
}
