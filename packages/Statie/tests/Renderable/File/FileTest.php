<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\File;

use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class FileTest extends AbstractContainerAwareTestCase
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        $configuration = $this->container->get(Configuration::class);
        $configuration->setSourceDirectory('sourceDirectory');

        $this->fileFactory = $this->container->get(FileFactory::class);
    }

    public function testGetRelativeSource(): void
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/FileFactorySource/someFile.latte');
        $file = $this->fileFactory->create($fileInfo);

        $this->assertStringEndsWith('/FileFactorySource/someFile.latte', $file->getRelativeSource());
    }

    public function testGetPrimaryExtension(): void
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/FileSource/some.html.latte');
        $file = $this->fileFactory->create($fileInfo);

        $this->assertSame('html', $file->getPrimaryExtension());
    }
}
