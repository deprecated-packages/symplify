<?php declare(strict_types=1);

namespace Symplify\Statie\Twig\Renderable;

use Nette\Utils\Strings;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Renderable\CodeBlocksProtector;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Templating\AbstractTemplatingFileDecorator;
use Symplify\Statie\Twig\TwigRenderer;

final class TwigFileDecorator extends AbstractTemplatingFileDecorator implements FileDecoratorInterface
{
    /**
     * @var TwigRenderer
     */
    private $twigRenderer;

    /**
     * @var CodeBlocksProtector
     */
    private $codeBlocksProtector;

    public function __construct(TwigRenderer $twigRenderer, CodeBlocksProtector $codeBlocksProtector)
    {
        $this->twigRenderer = $twigRenderer;
        $this->codeBlocksProtector = $codeBlocksProtector;
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

            if ($file->getLayout()) {
                $this->prependLayoutToFileContent($file, $file->getLayout());
            }

            $parameters = $this->createParameters($file, 'file');
            $content = $this->twigRenderer->renderFileWithParameters($file, $parameters);

            $file->changeContent($content);
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

            $this->prependLayoutToFileContent($file, $generatorElement->getLayout());

            $parameters = $this->createParameters($file, $generatorElement->getVariable());
            $content = $this->twigRenderer->renderFileWithParameters($file, $parameters);

            $content = $this->trimLayoutLeftover($content);

            $file->changeContent($content);
        }

        return $files;
    }

    /**
     * Higher priorities are executed first.
     */
    public function getPriority(): int
    {
        return 700;
    }

    /**
     * @inspiration https://github.com/sculpin/sculpin/blob/3264c087e31da2d49c9ec825fec38cae4d583d50/src/Sculpin/Bundle/TwigBundle/TwigFormatter.php#L113
     */
    private function prependLayoutToFileContent(AbstractFile $file, string $layout): void
    {
        $content = $file->getContent();

        // wrap to block
        if ($layout) {

            $contentWithPlaceholders = $this->codeBlocksProtector->replaceMarkdownCodeBlocksByPlaceholders($content);

            if (! Strings::match($contentWithPlaceholders, '#{% block content %}#')) {
                $content = '{% block content %}' . $content . '{% endblock %}';
            }

            // attach extends
            if (! Strings::match($contentWithPlaceholders, '#{% extends (.*?) %}#')) {
                $content = sprintf('{%% extends "%s" %%}', $layout) . PHP_EOL . $content;
            }
        }

        $file->changeContent($content);
    }

    private function trimLayoutLeftover(string $content): string
    {
        $content = Strings::replace($content, '#{% block (.*?) %}(.*?){% endblock %}#s', '$2', 1);

        return Strings::replace($content, '#{% extends [^}]+%}#', 1);
    }
}
