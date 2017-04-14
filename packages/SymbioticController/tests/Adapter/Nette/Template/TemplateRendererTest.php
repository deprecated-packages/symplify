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
    private $templateRenderer;

    protected function setUp(): void
    {
        $container = (new GeneralContainerFactory)->createFromConfig(__DIR__ . '/../config.neon');
        $this->templateRenderer = $container->getByType(TemplateRendererInterface::class);
    }

    public function testRenderFile(): void
    {
        $template = $this->templateRenderer->renderFileWithParameters(
            __DIR__ . '/TemplateRendererSource/someTemplate.latte'
        );

        $this->assertSame('Hi', trim($template));
    }

    public function testRenderFileWithParameters(): void
    {
        $template = $this->templateRenderer->renderFileWithParameters(
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
        $this->templateRenderer->renderFileWithParameters(
            __DIR__ . '/TemplateRendererSource/someTemplateWithPresenterHelper.latte'
        );
    }

    /**
     * @expectedException \Nette\Application\UI\InvalidLinkException
     * @expectedExceptionMessage Cannot load presenter "Homepage", class "HomepagePresenter" was not found.
     */
    public function testRenderFileWithPresenterWithMacro(): void
    {
        $this->templateRenderer->renderFileWithParameters(
            __DIR__ . '/TemplateRendererSource/someTemplateWithMacro.latte'
        );
    }
}
