<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource;

use Symplify\SymbioticController\Contract\Template\TemplateRendererInterface;

final class TemplateRendererPresenter
{
    /**
     * @var TemplateRendererInterface
     */
    private $templateRenderer;

    public function __construct(TemplateRendererInterface $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
    }

    public function __invoke(): string
    {
        return $this->templateRenderer->renderFileWithParameters(
            __DIR__ . '/templates/render-me.latte'
        );
    }
}
