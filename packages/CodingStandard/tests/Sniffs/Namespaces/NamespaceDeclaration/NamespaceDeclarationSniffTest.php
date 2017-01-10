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

        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong.php.inc'));
        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong2.php.inc'));
        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong3.php.inc'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct.php.inc'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct2.php.inc'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct3.php.inc'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct4.php.inc'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct5.php.inc'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct6.php.inc'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct7.php.inc'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct8.php.inc'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct9.php.inc'));
    }

    public function testFixing()
    {
        $codeSnifferRunner = new CodeSnifferRunner(NamespaceDeclarationSniff::NAME);

        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong.php.inc');
        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php.inc'), $fixedContent);

//        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong2.php.inc');
//        $this->assertSame(file_get_contents(__DIR__ . '/wrong-fixed.php.inc'), $fixedContent);
//
//        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong3.php.inc');
//        $this->assertSame(file_get_contents(__DIR__ . '/wrong3-fixed.php.inc'), $fixedContent);
//
//        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong4.php.inc');
//        $this->assertSame(file_get_contents(__DIR__ . '/wrong3-fixed.php.inc'), $fixedContent);
//
//        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong5.php.inc');
//        $this->assertSame(file_get_contents(__DIR__ . '/wrong5-fixed.php.inc'), $fixedContent);
//
//        $fixedContent = $codeSnifferRunner->getFixedContent(__DIR__ . '/wrong6.php.inc');
//        $this->assertSame(file_get_contents(__DIR__ . '/wrong6-fixed.php.inc'), $fixedContent);
    }
}
