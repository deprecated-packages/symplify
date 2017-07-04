<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Routing;

use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Renderable\Routing\Route\IndexRoute;
use Symplify\Statie\Renderable\Routing\Route\NotHtmlRoute;
use Symplify\Statie\Renderable\Routing\Route\PostRoute;
use Symplify\Statie\Renderable\Routing\RouteDecorator;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class RouteDecoratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var RouteDecorator
     */
    private $routeDecorator;

    protected function setUp(): void
    {
        /** @var Configuration $configuration */
        $configuration = $this->container->get(Configuration::class);
        $configuration->setPostRoute('blog/:title');
        $configuration->setSourceDirectory(__DIR__ . '/DecoratorSource');

        $this->routeDecorator = $this->createRouterDecorator($configuration);
    }

    public function test(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/RoutingDecoratorSource/someFile.latte');

        $configuration = $this->container->get(Configuration::class);
        $configuration->setSourceDirectory(__DIR__ . '/RoutingDecoratorSource');

        $this->routeDecorator->decorateFile($file);
        $this->assertSame('/someFile', $file->getRelativeUrl());
        $this->assertSame('/someFile' . DIRECTORY_SEPARATOR . 'index.html', $file->getOutputPath());
    }

    public function testStaticFile(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/RoutingDecoratorSource/static.css');

        $this->routeDecorator->decorateFile($file);
        $this->assertSame('static.css', $file->getRelativeUrl());
        $this->assertSame('static.css', $file->getOutputPath());
    }

    public function testIndexFile(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/RoutingDecoratorSource/index.html');

        $this->routeDecorator->decorateFile($file);
        $this->assertSame('index.html', $file->getOutputPath());
        $this->assertSame('/', $file->getRelativeUrl());

        $fileInfo = new SplFileInfo(__DIR__ . '/RoutingDecoratorSource/index.latte');
        $file = $this->getFileFactory()->create($fileInfo);

        $this->routeDecorator->decorateFile($file);
        $this->assertSame('index.html', $file->getOutputPath());
        $this->assertSame('/', $file->getRelativeUrl());
    }

    public function testPostFile(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/RoutingDecoratorSource/_posts/2016-10-10-somePost.html');

        $this->routeDecorator->decorateFile($file);
        $this->assertSame('blog/somePost', $file->getRelativeUrl());
    }

    private function getFileFactory(): FileFactory
    {
        $configuration = $this->container->get(Configuration::class);
        $configuration->setSourceDirectory('sourceDirectory');

        return new FileFactory($configuration);
    }

    private function createFileFromFilePath(string $filePath): AbstractFile
    {
        $fileInfo = new SplFileInfo($filePath);

        return $this->getFileFactory()
            ->create($fileInfo);
    }

    private function createRouterDecorator(Configuration $configuration): RouteDecorator
    {
        $routeDecorator = new RouteDecorator($configuration);
        $routeDecorator->addRoute(new IndexRoute);
        $routeDecorator->addRoute(new PostRoute($configuration));
        $routeDecorator->addRoute(new NotHtmlRoute);

        return $routeDecorator;
    }
}
