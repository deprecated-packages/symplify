<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Report;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\EventDispatcher\CurrentListenerSniffCodeProvider;
use Symplify\PHP7_CodeSniffer\Report\ErrorDataCollector;
use Symplify\PHP7_CodeSniffer\Report\ErrorMessageSorter;

final class ErrorDataCollectorTest extends TestCase
{
    /**
     * @var ErrorDataCollector
     */
    private $errorDataCollector;

    protected function setUp()
    {
        $this->errorDataCollector = new ErrorDataCollector(
            new CurrentListenerSniffCodeProvider(),
            new ErrorMessageSorter()
        );

        $this->errorDataCollector->addErrorMessage('filePath', 'Message', 5, 'Code', [], false);
    }

    public function testGetCounts()
    {
        $this->assertSame(1, $this->errorDataCollector->getErrorCount());
        $this->assertSame(0, $this->errorDataCollector->getFixableErrorCount());
        $this->assertSame(1, $this->errorDataCollector->getUnfixableErrorCount());
    }

    public function testGetErrorMessages()
    {
        $messages = $this->errorDataCollector->getErrorMessages();

        $this->assertSame([
            'filePath' => [
                [
                    'line' => 5,
                    'message' => 'Message',
                    'sniffCode' => '.Code',
                    'isFixable' => false
                ]
            ]
        ], $messages);
    }

    public function testGetUnfixableErrorMessage()
    {
        $this->assertSame(
            $this->errorDataCollector->getErrorMessages(),
            $this->errorDataCollector->getUnfixableErrorMessages()
        );

        $this->errorDataCollector->addErrorMessage('filePath', 'Message 2', 3, 'Code', [], true);

        $this->assertNotSame(
            $this->errorDataCollector->getErrorMessages(),
            $this->errorDataCollector->getUnfixableErrorMessages()
        );
    }
}
