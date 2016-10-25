<?php

declare(strict_types=1);

namespace Symplify\PHP7_Sculpin\Tests\Renderable\Routing;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\PHP7_Sculpin\Configuration\Configuration;
use Symplify\PHP7_Sculpin\Configuration\Parser\YamlAndNeonParser;
use Symplify\PHP7_Sculpin\Renderable\File\FileFactory;
use Symplify\PHP7_Sculpin\Renderable\Routing\Route\IndexRoute;
use Symplify\PHP7_Sculpin\Renderable\Routing\Route\NotHtmlRoute;
use Symplify\PHP7_Sculpin\Renderable\Routing\Route\PostRoute;
use Symplify\PHP7_Sculpin\Renderable\Routing\RouteDecorator;

final class RoutingDecoratorTest extends TestCase
{
    /**
     * @var RouteDecorator
     */
    private $routeDecorator;

    protected function setUp()
    {
        $configuration = new Configuration(new YamlAndNeonParser());
        $configuration->setPostRoute('blog/:title');

        $this->routeDecorator = new RouteDecorator();
        $this->routeDecorator->addRoute(new IndexRoute());
        $this->routeDecorator->addRoute(new PostRoute($configuration));
        $this->routeDecorator->addRoute(new NotHtmlRoute());
    }

    public function test()
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/RoutingDecoratorSource/someFile.latte');
        $file = $this->getFileFactory()->create($fileInfo);

        $this->routeDecorator->decorateFile($file);
        $this->assertSame('someFile', $file->getRelativeUrl());
        $this->assertSame('someFile' . DIRECTORY_SEPARATOR . 'index.html', $file->getOutputPath());
    }

    public function testStaticFile()
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/RoutingDecoratorSource/static.css');
        $file = $this->getFileFactory()->create($fileInfo);

        $this->routeDecorator->decorateFile($file);
        $this->assertSame('static.css', $file->getRelativeUrl());
        $this->assertSame('static.css', $file->getOutputPath());
    }

    public function testIndexFile()
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/RoutingDecoratorSource/index.html');
        $file = $this->getFileFactory()->create($fileInfo);

        $this->routeDecorator->decorateFile($file);
        $this->assertSame('index.html', $file->getOutputPath());
        $this->assertSame('/', $file->getRelativeUrl());

        $fileInfo = new SplFileInfo(__DIR__ . '/RoutingDecoratorSource/index.latte');
        $file = $this->getFileFactory()->create($fileInfo);

        $this->routeDecorator->decorateFile($file);
        $this->assertSame('index.html', $file->getOutputPath());
        $this->assertSame('/', $file->getRelativeUrl());
    }

    public function testPostFile()
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/RoutingDecoratorSource/_posts/2016-10-10-somePost.html');
        $file = $this->getFileFactory()->create($fileInfo);

        $this->routeDecorator->decorateFile($file);
        $this->assertSame('blog/somePost', $file->getRelativeUrl());
    }

    private function getFileFactory() : FileFactory
    {
        $configuration = new Configuration(new YamlAndNeonParser());
        $configuration->setSourceDirectory('sourceDirectory');

        return new FileFactory($configuration);
    }
}
