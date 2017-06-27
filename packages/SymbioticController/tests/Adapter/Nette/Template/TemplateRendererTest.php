<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Tests\Adapter\Nette\Template;

use Nette\Application\UI\InvalidLinkException;
use Nette\InvalidArgumentException;
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
                'name' => 'Tom',
            ]);

        $this->assertSame('Hi Tom', trim($template));
    }

    public function testRenderFileWithPresenterHelper(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Component with name \'someComponent\' does not exist');
        $this->templateRenderer->renderFileWithParameters(
            __DIR__ . '/TemplateRendererSource/someTemplateWithPresenterHelper.latte'
        );
    }

    public function testRenderFileWithPresenterWithMacro(): void
    {
        $this->expectException(InvalidLinkException::class);

        $this->expectExceptionMessage('Cannot load presenter "Homepage", class "HomepagePresenter" was not found.');
        $this->templateRenderer->renderFileWithParameters(
            __DIR__ . '/TemplateRendererSource/someTemplateWithMacro.latte'
        );
    }
}
