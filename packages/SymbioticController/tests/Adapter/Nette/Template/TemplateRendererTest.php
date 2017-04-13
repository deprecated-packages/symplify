<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Tests\Adapter\Nette\Template;

use Nette\Application\IRouter;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
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

        /** @var IRouter|RouteList $router */
        $router = $container->getByType(IRouter::class);
        $router[] = new Route('/you-are-welcome', 'Homepage:default');
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

    /**
     * @expectedException \Nette\Application\UI\InvalidLinkException
     * @expectedExceptionMessage Cannot load presenter "Homepage", class "HomepagePresenter" was not found.
     */
    public function testRenderFileWithPresenterWithMacro(): void
    {
        $this->templateRender->renderFileWithParameters(
            __DIR__ . '/TemplateRendererSource/someTemplateWithMacro.latte'
        );
    }
}
