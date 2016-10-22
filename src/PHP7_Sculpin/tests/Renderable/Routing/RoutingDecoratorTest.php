<?php

declare(strict_types=1);

namespace Symplify\PHP7_Sculpin\Tests\Renderable\Routing;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_Sculpin\Renderable\File\FileFactory;
use Symplify\PHP7_Sculpin\Renderable\Routing\RouteDecorator;

final class RoutingDecoratorTest extends TestCase
{
    /**
     * @var RouteDecorator
     */
    private $routeDecorator;

    protected function setUp()
    {
        $this->routeDecorator = new RouteDecorator('blog/:title');
    }

    public function test()
    {
        $fileInfo = new \SplFileInfo(__DIR__.'/RoutingDecoratorSource/someFile.latte');
        $file = (new FileFactory('sourceDirectory'))->create($fileInfo);

        $this->routeDecorator->decorateFile($file);
        $this->assertSame('someFile', $file->getRelativeUrl());
    }

    public function testStaticFile()
    {
        $fileInfo = new \SplFileInfo(__DIR__.'/RoutingDecoratorSource/static.css');
        $file = (new FileFactory('sourceDirectory'))->create($fileInfo);

        $this->routeDecorator->decorateFile($file);
        $this->assertSame('static.css', $file->getRelativeUrl());
    }

    public function testIndexFile()
    {
        $fileInfo = new \SplFileInfo(__DIR__.'/RoutingDecoratorSource/index.html');
        $file = (new FileFactory('sourceDirectory'))->create($fileInfo);

        $this->routeDecorator->decorateFile($file);
        $this->assertSame('index.html', $file->getRelativeUrl());

        $fileInfo = new \SplFileInfo(__DIR__.'/RoutingDecoratorSource/index.latte');
        $file = (new FileFactory('sourceDirectory'))->create($fileInfo);

        $this->routeDecorator->decorateFile($file);
        $this->assertSame('index.html', $file->getRelativeUrl());
    }

    public function testPostFile()
    {
        $fileInfo = new \SplFileInfo(__DIR__.'/RoutingDecoratorSource/_posts/2016-10-10-somePost.html');
        $file = (new FileFactory('sourceDirectory'))->create($fileInfo);

        $this->routeDecorator->decorateFile($file);
        $this->assertSame('blog/somePost', $file->getRelativeUrl());
    }
}
