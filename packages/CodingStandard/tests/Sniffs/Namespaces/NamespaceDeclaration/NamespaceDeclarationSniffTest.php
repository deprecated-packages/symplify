<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Namespaces\NamespaceDeclaration;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Tests\CodeSnifferRunner;
use SymplifyCodingStandard\Sniffs\Namespaces\NamespaceDeclarationSniff;

final class NamespaceDeclarationSniffTest extends TestCase
{

    public function testDetection()
    {
        $codeSnifferRunner = new CodeSnifferRunner(NamespaceDeclarationSniff::NAME);

        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong.php'));
        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong2.php'));
        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong3.php'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct.php'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct2.php'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct3.php'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct4.php'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct5.php'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct6.php'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct7.php'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct8.php'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct9.php'));
    }


    public function testFixing()
    {
        $codeSnifferRunner = new CodeSnifferRunner(NamespaceDeclarationSniff::NAME);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong.php');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php'), $fixedContent);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong2.php');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php'), $fixedContent);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong3.php');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong3-fixed.php'), $fixedContent);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong4.php');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong3-fixed.php'), $fixedContent);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong5.php');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong5-fixed.php'), $fixedContent);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong6.php');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong6-fixed.php'), $fixedContent);
    }
}
