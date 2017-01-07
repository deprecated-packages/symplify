<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Console\Style;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symplify\PHP7_CodeSniffer\Console\Style\CodeSnifferStyle;

final class CodeSnifferStyleTest extends TestCase
{
    /**
     * @var BufferedOutput
     */
    private $consoleOutput;

    /**
     * @var CodeSnifferStyle
     */
    private $codeSnifferStyle;

    protected function setUp()
    {
        $this->consoleOutput = new BufferedOutput();
        $this->codeSnifferStyle = new CodeSnifferStyle(new ArgvInput(), $this->consoleOutput);
    }

    public function testSuccess()
    {
        $this->codeSnifferStyle->success('ok message');
        $this->assertContains(' [OK] ok message ', $this->consoleOutput->fetch());
    }

    public function testError()
    {
        $this->codeSnifferStyle->error('error message');
        $this->assertContains(' [ERROR] error message ', $this->consoleOutput->fetch());
    }

    public function testWriteErrorReports()
    {
        $errorMessages['someFile.php'][] = [
            'line' => 1,
            'message' => 'some message',
            'sniffCode' => 'code',
            'isFixable'  => true
        ];

        $this->codeSnifferStyle->writeErrorReports($errorMessages);

        $this->assertContains(
            'FILE: someFile.php',
            $output = $this->consoleOutput->fetch()
        );
        $this->assertContains(
            '1      some message   code         YES',
            $output
        );
    }
}
