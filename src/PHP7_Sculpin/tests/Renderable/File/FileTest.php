<?php

declare(strict_types=1);

namespace Symplify\PHP7_Sculpin\Tests\Renderable\File;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\PHP7_Sculpin\Configuration\Configuration;
use Symplify\PHP7_Sculpin\Configuration\Parser\YamlAndNeonParser;
use Symplify\PHP7_Sculpin\Renderable\File\FileFactory;

final class FileTest extends TestCase
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp()
    {
        $configuration = new Configuration(new YamlAndNeonParser());
        $configuration->setSourceDirectory('sourceDirectory');

        $this->fileFactory = new FileFactory($configuration);
    }

    public function testGetRelativeSource()
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/FileFactorySource/someFile.latte');
        $file = $this->fileFactory->create($fileInfo);

        $this->assertStringEndsWith('/FileFactorySource/someFile.latte', $file->getRelativeSource());
    }

    public function testGetPrimaryExtension()
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/FileSource/some.html.latte');
        $file = $this->fileFactory->create($fileInfo);

        $this->assertSame('html', $file->getPrimaryExtension());
    }
}
