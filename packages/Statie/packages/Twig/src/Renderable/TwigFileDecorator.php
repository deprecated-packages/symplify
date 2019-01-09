<?php declare(strict_types=1);

namespace Symplify\Statie\Twig\Renderable;

use Nette\Utils\Strings;
use Symplify\Statie\Configuration\TemplatingDetector;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Renderable\CodeBlocksProtector;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Templating\AbstractTemplatingFileDecorator;
use Symplify\Statie\Twig\TwigRenderer;

final class TwigFileDecorator extends AbstractTemplatingFileDecorator implements FileDecoratorInterface
{
    /**
     * @var string
     */
    private const EXTEND_PATTERN = '#{% extends (.*?) %}#';

    /**
     * @var string
     */
    private const BLOCK_CONTENT_PATTERN = '#{% block content %}(.*){% endblock %}#s';

    /**
     * @var TwigRenderer
     */
    private $twigRenderer;

    /**
     * @var CodeBlocksProtector
     */
    private $codeBlocksProtector;

    /**
     * @var TemplatingDetector
     */
    private $templatingDetector;

    public function __construct(
        TwigRenderer $twigRenderer,
        CodeBlocksProtector $codeBlocksProtector,
        TemplatingDetector $templatingDetector
    ) {
        $this->twigRenderer = $twigRenderer;
        $this->codeBlocksProtector = $codeBlocksProtector;
        $this->templatingDetector = $templatingDetector;
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

            if ($file->getExtension() === 'md' && $this->templatingDetector->detect() === 'latte') {
                continue;
            }

            $layout = $this->normalizeLayoutSuffix($file->getLayout());
            $this->attachExtendsAndBlockContentToFileContent($file, $layout);

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

            if ($file->getExtension() === 'md' && $this->templatingDetector->detect() === 'latte') {
                continue;
            }

            $layout = $this->normalizeLayoutSuffix($file->getLayout() ?: $generatorElement->getLayout());
            $this->attachExtendsAndBlockContentToFileContent($file, $layout);

            $parameters = $this->createParameters($file, $generatorElement->getVariable());
            $content = $this->twigRenderer->renderFileWithParameters($file, $parameters);

            $content = $this->trimExtendsAndBlockContent($content);

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

    private function normalizeLayoutSuffix(?string $layout): ?string
    {
        if (Strings::endsWith($layout, '.latte')) {
            return Strings::replace($layout, '#\.latte$#', '.twig');
        }

        return $layout;
    }

    /**
     * @inspiration https://github.com/sculpin/sculpin/blob/3264c087e31da2d49c9ec825fec38cae4d583d50/src/Sculpin/Bundle/TwigBundle/TwigFormatter.php#L113
     */
    private function attachExtendsAndBlockContentToFileContent(AbstractFile $file, ?string $layout): void
    {
        if (! $layout) {
            return;
        }

        $content = $this->codeBlocksProtector->protectContentFromCallback(
            $file->getContent(),
            function (string $content) use ($layout) {
                if (! Strings::match($content, self::BLOCK_CONTENT_PATTERN)) {
                    $content = '{% block content %}' . $content . '{% endblock %}';
                }

                if (! Strings::match($content, self::EXTEND_PATTERN)) {
                    $content = '{% extends "' . $layout . '" %}' . $content;
                }

                return $content;
            }
        );

        $file->changeContent($content);
    }

    private function trimExtendsAndBlockContent(string $content): string
    {
        return $this->codeBlocksProtector->protectContentFromCallback($content, function (string $content) {
            $content = Strings::replace($content, self::BLOCK_CONTENT_PATTERN, '$1', 1);

            return Strings::replace($content, self::EXTEND_PATTERN, '', 1);
        });
    }
}
