<?php

declare(strict_types=1);

namespace Symplify\Statie\Tests\FileSystem;

use Nette\Utils\FileSystem;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\FileSystem\FileSystemWriter;
use Symplify\Statie\HttpKernel\StatieKernel;
use Symplify\Statie\Renderable\File\FileFactory;

final class FileSystemWriterTest extends AbstractKernelTestCase
{
    /**
     * @var string
     */
    private const SOURCE_DIRECTORY = __DIR__ . '/FileSystemWriterSource/source';

    /**
     * @var string
     */
    private const OUTPUT_DIRECTORY = __DIR__ . '/FileSystemWriterSource/output';

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
        $this->bootKernel(StatieKernel::class);

        $configuration = self::$container->get(StatieConfiguration::class);
        $configuration->setSourceDirectory(self::SOURCE_DIRECTORY);
        $configuration->setOutputDirectory(self::OUTPUT_DIRECTORY);

        $this->fileSystemWriter = self::$container->get(FileSystemWriter::class);

        $this->fileFactory = self::$container->get(FileFactory::class);
    }

    protected function tearDown(): void
    {
        FileSystem::delete(self::OUTPUT_DIRECTORY);
    }

    public function testCopyStaticFiles(): void
    {
        $file = new SmartFileInfo(self::SOURCE_DIRECTORY . '/index.html');
        $this->fileSystemWriter->copyStaticFiles([$file]);

        $this->assertFileEquals(self::SOURCE_DIRECTORY . '/index.html', self::OUTPUT_DIRECTORY . '/index.html');
    }

    public function testCopyRenderableFiles(): void
    {
        $fileInfo = new SmartFileInfo(self::SOURCE_DIRECTORY . '/contact.latte');
        $file = $this->fileFactory->createFromFileInfo($fileInfo);
        $file->setOutputPath('contact.html');

        $this->fileSystemWriter->renderFiles([$file]);

        $this->assertFileEquals(
            self::SOURCE_DIRECTORY . '/contact.latte',
            self::OUTPUT_DIRECTORY . '/contact.html'
        );
    }
}
