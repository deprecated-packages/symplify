<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Routing;

use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Renderable\RouteFileDecorator;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class RouteFileDecoratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var RouteFileDecorator
     */
    private $routeFileDecorator;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        /** @var Configuration $configuration */
        $configuration = $this->container->get(Configuration::class);
        $configuration->setSourceDirectory(__DIR__ . '/RouteFileDecoratorSource');

        $this->fileFactory = $this->container->get(FileFactory::class);
        $this->routeFileDecorator = $this->container->get(RouteFileDecorator::class);
    }

    public function test(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/RouteFileDecoratorSource/someFile.latte');

        /** @var Configuration $configuration */
        $configuration = $this->container->get(Configuration::class);
        $configuration->setSourceDirectory(__DIR__ . '/RouteFileDecoratorSource');

        $this->routeFileDecorator->decorateFiles([$file]);
        $this->assertSame('/someFile', $file->getRelativeUrl());
        $this->assertSame('/someFile' . DIRECTORY_SEPARATOR . 'index.html', $file->getOutputPath());
    }

    public function testStaticFile(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/RouteFileDecoratorSource/static.css');

        $this->routeFileDecorator->decorateFiles([$file]);
        $this->assertSame('static.css', $file->getRelativeUrl());
        $this->assertSame('static.css', $file->getOutputPath());
    }

    public function testIndexFile(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/RouteFileDecoratorSource/index.html');

        $this->routeFileDecorator->decorateFiles([$file]);
        $this->assertSame('index.html', $file->getOutputPath());
        $this->assertSame('/', $file->getRelativeUrl());

        $fileInfo = new SplFileInfo(__DIR__ . '/RouteFileDecoratorSource/index.latte');
        $file = $this->fileFactory->create($fileInfo);

        $this->routeFileDecorator->decorateFiles([$file]);
        $this->assertSame('index.html', $file->getOutputPath());
        $this->assertSame('/', $file->getRelativeUrl());
    }

    public function testPostFile(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/RouteFileDecoratorSource/_posts/2016-10-10-somePost.html');

        $this->routeFileDecorator->decorateFiles([$file]);
        $this->assertSame('blog/somePost', $file->getRelativeUrl());
    }

    private function createFileFromFilePath(string $filePath): AbstractFile
    {
        $fileInfo = new SplFileInfo($filePath);

        return $this->fileFactory->create($fileInfo);
    }
}
