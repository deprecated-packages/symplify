<?php

declare(strict_types=1);

namespace Symplify\PHP7_Sculpin\Tests\Output;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\PHP7_Sculpin\Output\FileSystemWriter;
use Symplify\PHP7_Sculpin\Renderable\File\File;

final class FileSystemWriterTest extends TestCase
{
    /**
     * @var string
     */
    private $sourceDirectory = __DIR__ . '/FileSystemWriterSource/source';

    /**
     * @var string
     */
    private $outputDirectory = __DIR__ . '/FileSystemWriterSource/output';

    /**
     * @var FileSystemWriter
     */
    private $fileSystemWriter;

    protected function setUp()
    {
        $this->fileSystemWriter = new FileSystemWriter($this->sourceDirectory, $this->outputDirectory);
    }

    public function testCopyStaticFiles()
    {
        $files = [new SplFileInfo($this->sourceDirectory . '/index.html')];
        $this->fileSystemWriter->copyStaticFiles($files);

        $this->assertFileEquals(
            $this->sourceDirectory . '/index.html',
            $this->outputDirectory . '/index.html'
        );
    }

    public function testCopyRenderableFiles()
    {
        $file = new File(
            new SplFileInfo($this->sourceDirectory . '/contact.latte'),
            'contact.html'
        );
        $file->setOutputPath('contact.html');

        $this->fileSystemWriter->copyRenderableFiles([$file]);

        $this->assertFileEquals(
            $this->sourceDirectory . '/contact.latte',
            $this->outputDirectory . '/contact.html'
        );
    }

    protected function tearDown()
    {
        FileSystem::delete($this->outputDirectory);
    }
}
