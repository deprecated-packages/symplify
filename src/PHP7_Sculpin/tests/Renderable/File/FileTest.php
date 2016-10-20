<?php

namespace Symplify\PHP7_Sculpin\Tests\Renderable\File;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\PHP7_Sculpin\Renderable\File\FileFactory;

final class FileTest extends TestCase
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp()
    {
        $this->fileFactory = new FileFactory('sourceDirectory');
    }

    public function testGetRelativeSource()
    {
        $fileInfo = new SplFileInfo(__DIR__.'/FileFactorySource/someFile.latte');
        $file = $this->fileFactory->create($fileInfo);

        $this->assertStringEndsWith('/FileFactorySource/someFile.latte', $file->getRelativeSource());
    }

    public function testGetPrimaryExtension()
    {
        $fileInfo = new SplFileInfo(__DIR__.'/FileSource/some.html.latte');
        $file = $this->fileFactory->create($fileInfo);

        $this->assertSame('html', $file->getPrimaryExtension());
    }
}
