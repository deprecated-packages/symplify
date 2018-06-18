<?php declare(strict_types=1);

namespace Symplify\Statie\Latte\Tests;

use Symfony\Component\Finder\SplFileInfo;
use Symplify\Statie\Latte\LatteRenderer;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class LatteRendererTest extends AbstractContainerAwareTestCase
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
        $this->latteRenderer = $this->container->get(LatteRenderer::class);
        $this->fileFactory = $this->container->get(FileFactory::class);
    }

    public function test(): void
    {
        $file = $this->fileFactory->createFromFileInfo(
            new SplFileInfo(__DIR__ . '/LatteRendererSource/latteWithCodeToHighlight.latte', '', '')
        );

        $rendered = $this->latteRenderer->renderExcludingHighlightBlocks($file, [
            'hi' => 'Welcome',
        ]);

        $this->assertStringEqualsFile(__DIR__ . '/LatteRendererSource/expectedCode.latte', $rendered);
    }
}
