<?php declare(strict_types=1);

namespace Symplify\Statie\Latte\Tests;

use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\HttpKernel\StatieKernel;
use Symplify\Statie\Latte\LatteRenderer;
use Symplify\Statie\Renderable\File\FileFactory;

final class LatteRendererTest extends AbstractKernelTestCase
{
    /**
     * @var LatteRenderer
     */
    private $latteRenderer;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        $this->bootKernel(StatieKernel::class);

        $this->latteRenderer = self::$container->get(LatteRenderer::class);
        $this->fileFactory = self::$container->get(FileFactory::class);

        $configuration = self::$container->get(StatieConfiguration::class);
        $configuration->setSourceDirectory(__DIR__ . '/LatteRendererSource');
    }

    public function test(): void
    {
        $file = $this->fileFactory->createFromFileInfo(
            new SmartFileInfo(__DIR__ . '/LatteRendererSource/latteWithCodeToHighlight.latte')
        );

        $rendered = $this->latteRenderer->renderFileWithParameters($file, [
            'hi' => 'Welcome',
        ]);

        $this->assertStringEqualsFile(__DIR__ . '/LatteRendererSource/expectedCode.latte', $rendered);
    }
}
