<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Output;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Configuration\Parser\NeonParser;
use Symplify\Statie\Output\FileSystemWriter;
use Symplify\Statie\Renderable\File\File;

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

    protected function setUp(): void
    {
        $configuration = new Configuration(new NeonParser);
        $configuration->setSourceDirectory($this->sourceDirectory);
        $configuration->setOutputDirectory($this->outputDirectory);

        $this->fileSystemWriter = new FileSystemWriter($configuration);
    }

    protected function tearDown(): void
    {
        FileSystem::delete($this->outputDirectory);
    }

    public function testCopyStaticFiles(): void
    {
        $files = [new SplFileInfo($this->sourceDirectory . '/index.html')];
        $this->fileSystemWriter->copyStaticFiles($files);

        $this->assertFileEquals(
            $this->sourceDirectory . '/index.html',
            $this->outputDirectory . '/index.html'
        );
    }

    public function testCopyRenderableFiles(): void
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
}
