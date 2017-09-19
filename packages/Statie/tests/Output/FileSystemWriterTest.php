<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Output;

use Nette\Utils\FileSystem;
use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Output\FileSystemWriter;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class FileSystemWriterTest extends AbstractContainerAwareTestCase
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

    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        /** @var Configuration $configuration */
        $configuration = $this->container->get(Configuration::class);
        $configuration->setSourceDirectory($this->sourceDirectory);
        $configuration->setOutputDirectory($this->outputDirectory);

        $this->fileSystemWriter = $this->container->get(FileSystemWriter::class);

        $this->fileFactory = $this->container->get(FileFactory::class);
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
        $file = $this->fileFactory->createFromFilePath($this->sourceDirectory . '/contact.latte');
        $file->setOutputPath('contact.html');

        $this->fileSystemWriter->copyRenderableFiles([$file]);

        $this->assertFileEquals(
            $this->sourceDirectory . '/contact.latte',
            $this->outputDirectory . '/contact.html'
        );
    }
}
