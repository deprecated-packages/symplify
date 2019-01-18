<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Routing;

use Iterator;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\Statie\Configuration\StatieConfiguration;
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
        $configuration = $this->container->get(StatieConfiguration::class);
        $configuration->setSourceDirectory(__DIR__ . '/RouteFileDecoratorSource');

        $this->fileFactory = $this->container->get(FileFactory::class);
        $this->routeFileDecorator = $this->container->get(RouteFileDecorator::class);

        $configuration = $this->container->get(StatieConfiguration::class);
        $configuration->setSourceDirectory(__DIR__ . '/RouteFileDecoratorSource');
    }

    /**
     * @dataProvider provideFileAndOutputAndRelativePath()
     */
    public function test(string $fileName, string $relativeUrl, string $outputPath): void
    {
        $fileInfo = new SmartFileInfo($fileName);
        $file = $this->fileFactory->createFromFileInfo($fileInfo);
        $this->routeFileDecorator->decorateFiles([$file]);

        $this->assertSame($relativeUrl, $file->getRelativeUrl());
        $this->assertSame($outputPath, $file->getOutputPath());
    }

    public function provideFileAndOutputAndRelativePath(): Iterator
    {
        yield [__DIR__ . '/RouteFileDecoratorSource/someFile.latte', '/someFile', '/someFile/index.html'];
        yield [__DIR__ . '/RouteFileDecoratorSource/index.html', '/', 'index.html'];
        yield [__DIR__ . '/RouteFileDecoratorSource/index.latte', '/', 'index.html'];
    }
}
