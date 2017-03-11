<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Routing;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Configuration\Parser\NeonParser;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Renderable\Routing\Route\IndexRoute;
use Symplify\Statie\Renderable\Routing\Route\NotHtmlRoute;
use Symplify\Statie\Renderable\Routing\Route\PostRoute;
use Symplify\Statie\Renderable\Routing\RouteDecorator;

final class RouteDecoratorTest extends TestCase
{
    /**
     * @var RouteDecorator
     */
    private $routeDecorator;

    protected function setUp(): void
    {
        $configuration = new Configuration(new NeonParser);
        $configuration->loadFromArray([
            'configuration' => [
                Configuration::OPTION_POST_ROUTE => 'blog/:title'
            ]
        ]);

        $configuration->setSourceDirectory(__DIR__ . '/DecoratorSource');

        $this->routeDecorator = $this->createRouterDecorator($configuration);
    }

    public function test(): void
    {
        $file = $this->createFileFromFilePath(__DIR__ . '/RoutingDecoratorSource/someFile.latte');

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
        $configuration = new Configuration(new NeonParser);
        $configuration->setSourceDirectory('sourceDirectory');

        return new FileFactory($configuration);
    }

    private function createFileFromFilePath(string $filePath): AbstractFile
    {
        $fileInfo = new SplFileInfo($filePath);

        return $this->getFileFactory()
            ->create($fileInfo);
    }

    private function createRouterDecorator($configuration): RouteDecorator
    {
        $routeDecorator = new RouteDecorator($configuration);
        $routeDecorator->addRoute(new IndexRoute);
        $routeDecorator->addRoute(new PostRoute($configuration));
        $routeDecorator->addRoute(new NotHtmlRoute);

        return $routeDecorator;
    }
}
