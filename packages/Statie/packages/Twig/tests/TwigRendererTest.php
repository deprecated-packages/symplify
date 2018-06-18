<?php declare(strict_types=1);

namespace Symplify\Statie\Twig\Tests;

use Symfony\Component\Finder\SplFileInfo;
use Symplify\Statie\Twig\TwigRenderer;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

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

    protected function setUp(): void
    {
        $this->twigRenderer = $this->container->get(TwigRenderer::class);
        $this->fileFactory = $this->container->get(FileFactory::class);
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
}
