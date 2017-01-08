<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\File;

use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Configuration\Parser\NeonParser;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\File;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Renderable\File\PostFile;

final class FileFactoryTest extends TestCase
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp()
    {
        $configuration = new Configuration(new NeonParser());
        $configuration->setSourceDirectory('sourceDirectory');
        $this->fileFactory = new FileFactory($configuration);
    }

    public function test()
    {
        $file = $this->createFileFromPath(__DIR__ . '/FileFactorySource/someFile.latte');

        $this->assertInstanceOf(File::class, $file);
        $this->assertNotInstanceOf(PostFile::class, $file);

        $this->assertSame('latte', $file->getPrimaryExtension());
        $this->assertSame([], $file->getConfiguration());
        $this->assertSame('Some content', $file->getContent());

        $file->setOutputPath('someRemoteFile' . DIRECTORY_SEPARATOR . 'index.html');
        $file->setRelativeUrl('someRemoteFile');

        $this->assertSame('someRemoteFile', $file->getRelativeUrl());
        $this->assertSame('someFile', $file->getBaseName());
        $this->assertSame('', $file->getLayout());
    }

    public function testPost()
    {
        $postFile = $this->createFileFromPath(__DIR__ . '/FileFactorySource/_posts/2016-01-01-somePost.latte');

        $this->assertInstanceOf(PostFile::class, $postFile);

        $this->assertInstanceOf(DateTimeInterface::class, $postFile->getDate());
        $this->assertSame('2016-01-01', $postFile->getDateInFormat('Y-m-d'));
        $this->assertSame('somePost', $postFile->getFilenameWithoutDate());
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidPostName()
    {
        $this->createFileFromPath(__DIR__ . '/FileFactorySource/_posts/somePost.latte');
    }

    /**
     * @return File|PostFile
     */
    private function createFileFromPath(string $filePath)
    {
        $fileInfo = new SplFileInfo($filePath);

        return $this->fileFactory->create($fileInfo);
    }
}
