<?php

namespace Symplify\PHP7_CdeSniffer\Tests\Application;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Application\FileProcessor;
use Symplify\PHP7_CodeSniffer\Application\Fixer;
use Symplify\PHP7_CodeSniffer\EventDispatcher\CurrentListenerSniffCodeProvider;
use Symplify\PHP7_CodeSniffer\EventDispatcher\SniffDispatcher;
use Symplify\PHP7_CodeSniffer\File\FileFactory;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class FileProcessorTest extends TestCase
{
    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp()
    {
        $this->fileProcessor = new FileProcessor(new SniffDispatcher(new CurrentListenerSniffCodeProvider()), new Fixer());
        $this->fileFactory = Instantiator::createFileFactory();
    }

    public function testProcessFiles()
    {
        $file = $this->fileFactory->create(__DIR__.'/FileProcessorSource/SomeFile.php', false);
        $this->fileProcessor->processFiles([$file], false);

        $file = $this->fileFactory->create(__DIR__.'/FileProcessorSource/SomeFile.php', true);
        $this->fileProcessor->processFiles([$file], true);
    }
}
