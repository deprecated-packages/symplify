<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\File;

use DateTimeInterface;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Renderable\File\File;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Renderable\File\PostFile;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class FileFactoryTest extends AbstractContainerAwareTestCase
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        /** @var Configuration $configuration */
        $configuration = $this->container->get(Configuration::class);
        $configuration->setSourceDirectory(__DIR__ . '/FileFactorySource');

        $this->fileFactory = $this->container->get(FileFactory::class);
    }

    public function test(): void
    {
        $file = $this->fileFactory->createFromFilePath(__DIR__ . '/FileFactorySource/someFile.latte');

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

    public function testPost(): void
    {
        /** @var PostFile $postFile */
        $postFile = $this->fileFactory->createFromFilePath(
            __DIR__ . '/FileFactorySource/_posts/2016-01-01-somePost.latte'
        );

        $this->assertInstanceOf(PostFile::class, $postFile);

        $this->assertInstanceOf(DateTimeInterface::class, $postFile->getDate());
        $this->assertSame('2016-01-01', $postFile->getDateInFormat('Y-m-d'));
        $this->assertSame('somePost', $postFile->getFilenameWithoutDate());
    }
}
