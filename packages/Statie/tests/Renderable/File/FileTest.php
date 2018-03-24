<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\File;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;
use Symplify\PackageBuilder\Finder\SymfonyFileInfoFactory;

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

    public function test(): void
    {
        $fileInfo = SymfonyFileInfoFactory::createFromFilePath(__DIR__ . '/FileFactorySource/someFile.html.latte');
        $file = $this->fileFactory->createFromFileInfo($fileInfo);

        $this->assertSame('', $file->getRelativeSource());

        $this->assertSame('html', $file->getPrimaryExtension());
    }
}
