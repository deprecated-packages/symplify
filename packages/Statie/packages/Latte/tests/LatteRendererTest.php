<?php declare(strict_types=1);

namespace Symplify\Statie\Latte\Tests;

use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\Statie\Configuration\StatieConfiguration;
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

        /** @var StatieConfiguration $configuration */
        $configuration = $this->container->get(StatieConfiguration::class);
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
