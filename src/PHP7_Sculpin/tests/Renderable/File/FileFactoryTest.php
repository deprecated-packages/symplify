<?php

namespace Symplify\PHP7_Sculpin\Tests\Renderable\File;

use DateTimeInterface;
use Exception;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\PHP7_Sculpin\Renderable\File\File;
use Symplify\PHP7_Sculpin\Renderable\File\FileFactory;
use Symplify\PHP7_Sculpin\Renderable\File\PostFile;

final class FileFactoryTest extends TestCase
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp()
    {
        $this->fileFactory = new FileFactory('sourceDirectory');
    }

    public function test()
    {
        $file = $this->createFileFromPath(__DIR__.'/FileFactorySource/someFile.latte');

        $this->assertInstanceOf(File::class, $file);
        $this->assertNotInstanceOf(PostFile::class, $file);

        $this->assertSame('latte', $file->getPrimaryExtension());
        $this->assertSame([], $file->getConfiguration());
        $this->assertSame('Some content', $file->getContent());

        $file->setOutputPath('someRemoteFile/index.html');
        $this->assertSame('someRemoteFile', $file->getRelativeUrl());
        $this->assertSame('someFile', $file->getBaseName());
        $this->assertSame('', $file->getLayout());
    }

    public function testPost()
    {
        $postFile = $this->createFileFromPath(__DIR__.'/FileFactorySource/_posts/2016-01-01-somePost.latte');

        $this->assertInstanceOf(PostFile::class, $postFile);

        $this->assertInstanceOf(DateTimeInterface::class, $postFile->getDate());
        $this->assertSame('2016-01-01', $postFile->getDateInFormat('Y-m-d'));
        $this->assertSame('somePost', $postFile->getFilenameWithoutDate());
    }

    /**
     * @expectedException Exception
     */
    public function testInvalidPostName()
    {
        $this->createFileFromPath(__DIR__.'/FileFactorySource/_posts/somePost.latte');
    }

    /**
     * @return File|PostFile
     */
    private function createFileFromPath(string $filePath) : File
    {
        $fileInfo = new SplFileInfo($filePath);

        return $this->fileFactory->create($fileInfo);
    }
}
