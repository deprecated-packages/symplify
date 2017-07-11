<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Adapter\Nette\Template;

use Nette\Application\UI\ITemplateFactory;
use Nette\Bridges\ApplicationLatte\TemplateFactory;
use Symplify\SymbioticController\Adapter\Nette\Application\PresenterHelper;
use Symplify\SymbioticController\Contract\Template\TemplateRendererInterface;

final class TemplateRenderer implements TemplateRendererInterface
{
    /**
     * @var TemplateFactory|ITemplateFactory
     */
    private $templateFactory;

    /**
     * @var PresenterHelper
     */
    private $presenterHelper;

    public function __construct(ITemplateFactory $templateFactory, PresenterHelper $presenterHelper)
    {
        $this->templateFactory = $templateFactory;
        $this->presenterHelper = $presenterHelper;
    }

    /**
     * @param mixed[] $parameters
     */
    public function renderFileWithParameters(string $file, array $parameters = []): string
    {
        $template = $this->templateFactory->createTemplate();
        $latte = $template->getLatte();

        $layout = $this->guessLayoutFromFile($file);
        $this->presenterHelper->setLayout($layout);
        $latte->addProvider('uiControl', $this->presenterHelper);

        return $latte->renderToString($file, $parameters + $template->getParameters());
    }

    private function guessLayoutFromFile(string $file): string
    {
        // @todo: add test for {extends "..."} later
        // @similar to magic in UI\Presenter
        $possibleLayoutLocations = [];
        $possibleLayoutLocations[] = dirname($file) . DIRECTORY_SEPARATOR . '@layout.latte';
        foreach ($possibleLayoutLocations as $possibleLayoutLocation) {
            if (is_file($possibleLayoutLocation)) {
                return $possibleLayoutLocation;
            }
        }

        // @todo: fail here
        return '';
    }
}
