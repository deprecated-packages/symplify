<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Routing;

use Symplify\PackageBuilder\Finder\SymfonyFileInfoFactory;
use Symplify\Statie\Configuration\Configuration;
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

    /**
     * @dataProvider provideFileAndOutputAndRelativePath()
     */
    public function test(string $fileName, string $relativeUrl, string $outputPath): void
    {
        $fileInfo = SymfonyFileInfoFactory::createFromFilePath($fileName);
        $file = $this->fileFactory->createFromFileInfo($fileInfo);
        $this->routeFileDecorator->decorateFiles([$file]);

        $this->assertSame($relativeUrl, $file->getRelativeUrl());
        $this->assertSame($outputPath, $file->getOutputPath());
    }

    /**
     * @return string[][]
     */
    public function provideFileAndOutputAndRelativePath(): array
    {
        return [
            [__DIR__ . '/RouteFileDecoratorSource/someFile.latte', '/someFile', '/someFile/index.html'],
            [__DIR__ . '/RouteFileDecoratorSource/index.html', '/', 'index.html'],
            [__DIR__ . '/RouteFileDecoratorSource/index.latte', '/', 'index.html'],
        ];
    }
}
