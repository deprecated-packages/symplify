<?php declare(strict_types=1);

namespace Symplify\Statie\Twig\Tests;

use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\HttpKernel\StatieKernel;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Twig\TwigRenderer;

final class TwigRendererTest extends AbstractKernelTestCase
{
    /**
     * @var TwigRenderer
     */
    private $twigRenderer;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        $this->bootKernel(StatieKernel::class);

        $this->twigRenderer = self::$container->get(TwigRenderer::class);
        $this->fileFactory = self::$container->get(FileFactory::class);

        $configuration = self::$container->get(StatieConfiguration::class);
        $configuration->setSourceDirectory(__DIR__ . '/TwigRendererSource');
    }

    public function test(): void
    {
        $file = $this->fileFactory->createFromFileInfo(
            new SmartFileInfo(__DIR__ . '/TwigRendererSource/codeToHighlight.twig')
        );

        $rendered = $this->twigRenderer->renderFileWithParameters($file, [
            'hi' => 'Welcome',
        ]);

        $this->assertStringEqualsFile(__DIR__ . '/TwigRendererSource/expectedCode.twig', $rendered);
    }
}
