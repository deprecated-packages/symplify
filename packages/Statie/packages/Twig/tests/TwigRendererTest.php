<?php declare(strict_types=1);

namespace Symplify\Statie\Twig\Tests;

use Symfony\Component\Finder\SplFileInfo;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;
use Symplify\Statie\Twig\TwigRenderer;
use Twig\Loader\ArrayLoader;

final class TwigRendererTest extends AbstractContainerAwareTestCase
{
    /**
     * @var TwigRenderer
     */
    private $twigRenderer;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var ArrayLoader
     */
    private $twigArrayLoader;

    protected function setUp(): void
    {
        $this->twigRenderer = $this->container->get(TwigRenderer::class);
        $this->fileFactory = $this->container->get(FileFactory::class);
        $this->twigArrayLoader = $this->container->get(ArrayLoader::class);
    }

    public function test(): void
    {
        $file = $this->fileFactory->createFromFileInfo(
            new SplFileInfo(__DIR__ . '/TwigRendererSource/codeToHighlight.twig', '', '')
        );

        $rendered = $this->twigRenderer->renderExcludingHighlightBlocks($file, [
            'hi' => 'Welcome',
        ]);

        $this->assertStringEqualsFile(__DIR__ . '/TwigRendererSource/expectedCode.twig', $rendered);
    }

    public function testLayout(): void
    {
        $file = $this->fileFactory->createFromFileInfo(
            new SplFileInfo(__DIR__ . '/TwigRendererSource/codeWithLayout.twig', '', '')
        );

        $this->twigArrayLoader->setTemplate('_layouts/someLayout.latte', file_get_contents(__DIR__ . '/TwigRendererSource/expectedWithLayout.html'));
        $file->addConfiguration(['layout' => '_layouts/someLayout.latte']);

        $rendered = $this->twigRenderer->renderExcludingHighlightBlocks($file,  []);

        $this->assertStringEqualsFile(__DIR__ . '/TwigRendererSource/expectedWithLayout.html', $rendered);
    }
}
