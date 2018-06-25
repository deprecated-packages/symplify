<?php declare(strict_types=1);

namespace Symplify\Statie\Twig\Renderable;

use Nette\Utils\Strings;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Twig\TwigRenderer;

final class TwigFileDecorator implements FileDecoratorInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var TwigRenderer
     */
    private $twigRenderer;

    public function __construct(Configuration $configuration, TwigRenderer $twigRenderer)
    {
        $this->configuration = $configuration;
        $this->twigRenderer = $twigRenderer;
    }

    /**
     * @param AbstractFile[] $files
     * @return AbstractFile[]
     */
    public function decorateFiles(array $files): array
    {
        foreach ($files as $file) {
            if (! in_array($file->getExtension(), ['twig', 'md'], true)) {
                continue;
            }

            $this->decorateFile($file);
        }

        return $files;
    }

    /**
     * @param AbstractFile[] $files
     * @return AbstractFile[]
     */
    public function decorateFilesWithGeneratorElement(array $files, GeneratorElement $generatorElement): array
    {
        foreach ($files as $file) {
            if (! in_array($file->getExtension(), ['twig', 'md'], true)) {
                continue;
            }

            $this->decorateFileWithGeneratorElements($file, $generatorElement);
        }

        return $files;
    }

    private function decorateFile(AbstractFile $file): void
    {
        $parameters = $file->getConfiguration() + $this->configuration->getOptions() + [
            'file' => $file,
        ];

        $htmlContent = $this->renderOuterWithLayout($file, $parameters);
        $file->changeContent($htmlContent);
    }

    /**
     * @param mixed[] $parameters
     */
    private function renderOuterWithLayout(AbstractFile $file, array $parameters): string
    {
        if ($file->getLayout()) {
            $this->prependLayoutToFileContent($file, $file->getLayout());
        }

        return $this->twigRenderer->renderExcludingHighlightBlocks($file, $parameters);
    }

    /**
     * Higher priorities are executed first.
     */
    public function getPriority(): int
    {
        return 700;
    }

    private function decorateFileWithGeneratorElements(AbstractFile $file, GeneratorElement $generatorElement): void
    {
        // prepare parameters
        $parameters = $file->getConfiguration() + $this->configuration->getOptions() + [
            $generatorElement->getVariable() => $file,
            'layout' => $generatorElement->getLayout(),
        ];

        // add layout
        $this->prependLayoutToFileContent($file, $generatorElement->getLayout());

        $htmlContent = $this->twigRenderer->renderExcludingHighlightBlocks($file, $parameters);

        // trim "{% extends %s %}" left over
        $htmlContent = Strings::replace($htmlContent, '#{% extends "[a-z]+" %}#');

        $file->changeContent($htmlContent);
    }

    /**
     * @inspiration https://github.com/sculpin/sculpin/blob/3264c087e31da2d49c9ec825fec38cae4d583d50/src/Sculpin/Bundle/TwigBundle/TwigFormatter.php#L113
     */
    private function prependLayoutToFileContent(AbstractFile $file, string $layout): void
    {
        $content = $file->getContent();

        // wrap to block
        if (! Strings::match($content, '#{% block content %}#')) {
            $content = '{% block content %}' . $content . '{% endblock %}';
        }

        $file->changeContent(sprintf('{%% extends "%s" %%}', $layout) . PHP_EOL . $content);
    }
}
