<?php declare(strict_types=1);

namespace Symplify\Statie\FlatWhite\Tests\Latte;

use Symplify\Statie\FlatWhite\Latte\LatteRenderer;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class LatteRendererTest extends AbstractContainerAwareTestCase
{
    /**
     * @var LatteRenderer
     */
    private $latteRenderer;

    protected function setUp(): void
    {
        $this->latteRenderer = $this->container->get(LatteRenderer::class);
    }

    public function test(): void
    {
        $templateFileContent = file_get_contents(__DIR__ . '/LatteRendererSource/latteWithCodeToHighlight.latte');
        $rendered = $this->latteRenderer->renderExcludingHighlightBlocks($templateFileContent, [
            'hi' => 'Welcome',
        ]);

        $expectedFileContent = file_get_contents(__DIR__ . '/LatteRendererSource/expectedCode.latte');
        $this->assertSame($expectedFileContent, $rendered);
    }
}
