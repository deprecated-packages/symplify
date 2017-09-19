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
        /** @var Configuration $configuration */
        $configuration = $this->container->get(Configuration::class);
        $configuration->setSourceDirectory(__DIR__ . '/FileFactorySource');

        $this->fileFactory = $this->container->get(FileFactory::class);
    }

    public function testGetRelativeSource(): void
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/FileFactorySource/someFile.latte');
        $file = $this->fileFactory->create($fileInfo);

        $this->assertSame('someFile.latte', $file->getRelativeSource());
    }

    public function testGetPrimaryExtension(): void
    {
        $file = $this->fileFactory->createFromFilePath(__DIR__ . '/FileSource/some.html.latte');

        $this->assertSame('html', $file->getPrimaryExtension());
    }
}
