<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Tests\Adapter\Nette\Template;

use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;
use Symplify\SymbioticController\Adapter\Nette\Template\TemplateRenderer;
use Symplify\SymbioticController\Contract\Template\TemplateRendererInterface;

final class TemplateRendererTest extends TestCase
{
    /**
     * @var TemplateRenderer|TemplateRendererInterface
     */
    private $templateRender;

    protected function setUp(): void
    {
        $container = (new GeneralContainerFactory)->createFromConfig(__DIR__ . '/../config.neon');
        $this->templateRender = $container->getByType(TemplateRendererInterface::class);
    }

    public function testRenderFile(): void
    {
        $template = $this->templateRender->renderFileWithParameters(
            __DIR__ . '/TemplateRendererSource/someTemplate.latte'
        );

        $this->assertSame('Hi', trim($template));
    }

    public function testRenderFileWithParameters(): void
    {
        $template = $this->templateRender->renderFileWithParameters(
            __DIR__ . '/TemplateRendererSource/someTemplateWithVariable.latte', [
                'name' => 'Tom'
            ]);

        $this->assertSame('Hi Tom', trim($template));
    }

    /**
     * @expectedException \Nette\InvalidArgumentException
     * @expectedExceptionMessage Component with name 'someComponent' does not exist
     */
    public function testRenderFileWithPresenterHelper(): void
    {
        $this->templateRender->renderFileWithParameters(
            __DIR__ . '/TemplateRendererSource/someTemplateWithPresenterHelper.latte'
        );
    }
}
