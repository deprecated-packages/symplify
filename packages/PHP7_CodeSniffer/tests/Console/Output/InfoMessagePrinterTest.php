<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Console\Output;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symplify\PHP7_CodeSniffer\Console\Output\InfoMessagePrinter;
use Symplify\PHP7_CodeSniffer\Console\Style\CodeSnifferStyle;
use Symplify\PHP7_CodeSniffer\Report\ErrorDataCollector;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class InfoMessagePrinterTest extends TestCase
{
    /**
     * @var InfoMessagePrinter
     */
    private $infoMessagePrinter;

    /**
     * @var BufferedOutput
     */
    private $consoleOutput;

    /**
     * @var ErrorDataCollector
     */
    private $errorDataCollector;

    protected function setUp()
    {
        $this->infoMessagePrinter = new InfoMessagePrinter(
            $this->createCodeSnifferStyle(),
            $this->errorDataCollector = Instantiator::createErrorDataCollector()
        );
    }

    public function testNothingPrinterForNoErrors()
    {
        $this->assertSame('', $this->consoleOutput->fetch());

        $this->infoMessagePrinter->printFoundErrorsStatus(true);
        $this->assertSame('', $this->consoleOutput->fetch());
    }

    public function testForNormalError()
    {
        $this->errorDataCollector->addErrorMessage('someFile.php', 'Message', 1, 'Code');

        $this->infoMessagePrinter->printFoundErrorsStatus(true);
        $this->assertContains(
            '[ERROR] 1 error(s) could not be fixed. You have to do it manually.',
            $this->consoleOutput->fetch()
        );
    }

    public function testFixableError()
    {
        $this->errorDataCollector->addErrorMessage('someFile.php', 'Message', 1, 'Code', [], true);
        $this->errorDataCollector->addErrorMessage(
            'someOtherFile.php',
            'Other Message',
            1,
            'Code',
            [],
            false
        );

        $this->infoMessagePrinter->printFoundErrorsStatus(true);
        $this->assertContains(
            '[ERROR] 1 error(s) could not be fixed. You have to do it manually.',
            $output = $this->consoleOutput->fetch()
        );

        $this->assertContains(
            '[OK] Congrats! 1 error(s) were fixed.',
            $output
        );
    }

    public function testFixableErrorWithoutFixer()
    {
        $this->errorDataCollector->addErrorMessage('someFile.php', 'Message', 1, 'Code', [], true);

        $this->infoMessagePrinter->printFoundErrorsStatus(false);
        $this->assertContains(
            '[OK] Good news is, we can fix ALL of them for you. Just add "--fix".',
            $output = $this->consoleOutput->fetch()
        );
        $this->assertContains(
            '[ERROR] 1 error(s) found.',
            $output
        );
    }

    private function createCodeSnifferStyle() : CodeSnifferStyle
    {
        return new CodeSnifferStyle(
            new ArgvInput(),
            $this->consoleOutput = new BufferedOutput()
        );
    }
}
