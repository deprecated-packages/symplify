<?php declare(strict_types=1);

namespace Symplify\Statie\Amp\Tests;

use SplFileInfo;
use Symplify\Statie\Amp\AmpLinkFileDecorator;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class AmpLinkFileDecoratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var AmpLinkFileDecorator
     */
    private $ampLinkFileDecorator;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        $this->ampLinkFileDecorator = $this->container->get(AmpLinkFileDecorator::class);
        $this->fileFactory = $this->container->get(FileFactory::class);
    }

    public function testFilesAreNotDecorated(): void
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/HtmlToAmpConvertorSource/file.html');
        $file = $this->fileFactory->create($fileInfo);

        $decoratedFiles = $this->ampLinkFileDecorator->decorateFiles([
            $file,
        ]);

        $this->assertSame($file->getContent(), $decoratedFiles[0]->getContent());
    }
}
