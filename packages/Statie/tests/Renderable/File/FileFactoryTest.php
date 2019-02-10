<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\File;

use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\HttpKernel\StatieKernel;
use Symplify\Statie\Renderable\File\File;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Renderable\File\PostFile;

final class FileFactoryTest extends AbstractKernelTestCase
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        $this->bootKernel(StatieKernel::class);

        $configuration = self::$container->get(StatieConfiguration::class);
        $configuration->setSourceDirectory(__DIR__ . '/FileFactorySource');

        $this->fileFactory = self::$container->get(FileFactory::class);
    }

    public function testCreateFromFileInfo(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/FileFactorySource/someFile.latte');
        $file = $this->fileFactory->createFromFileInfo($fileInfo);

        $this->assertInstanceOf(File::class, $file);
        $this->assertNotInstanceOf(PostFile::class, $file);

        $this->assertSame('latte', $file->getPrimaryExtension());
        $this->assertSame([], $file->getConfiguration());
        $this->assertSame('Some content', $file->getContent());

        $file->setOutputPath('someRemoteFile' . DIRECTORY_SEPARATOR . 'index.html');
        $file->setRelativeUrl('someRemoteFile');

        $this->assertSame('someRemoteFile', $file->getRelativeUrl());
        $this->assertSame('someFile', $file->getBaseName());
        $this->assertNull($file->getLayout());
    }
}
