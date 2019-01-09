<?php declare(strict_types=1);

namespace Symplify\Statie\Latte\Renderable;

use Nette\Utils\Strings;
use Symplify\Statie\Configuration\TemplatingDetector;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Latte\LatteRenderer;
use Symplify\Statie\Renderable\CodeBlocksProtector;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Templating\AbstractTemplatingFileDecorator;

final class LatteFileDecorator extends AbstractTemplatingFileDecorator implements FileDecoratorInterface
{
    /**
     * @var string
     */
    private const LAYOUT_PATTERN = '#{layout (.*?)}#';

    /**
     * @var string
     */
    private const BLOCK_CONTENT_PATTERN = '#{block content}(.*){/block}#s';

    /**
     * @var LatteRenderer
     */
    private $latteRenderer;

    /**
     * @var CodeBlocksProtector
     */
    private $codeBlocksProtector;

    /**
     * @var TemplatingDetector
     */
    private $templatingDetector;

    public function __construct(
        LatteRenderer $latteRenderer,
        CodeBlocksProtector $codeBlocksProtector,
        TemplatingDetector $templatingDetector
    ) {
        $this->latteRenderer = $latteRenderer;
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
            if (! in_array($file->getExtension(), ['latte', 'md'], true)) {
                continue;
            }

            if ($file->getExtension() === 'md' && $this->templatingDetector->detect() === 'twig') {
                continue;
            }

            $layout = $this->normalizeLayoutSuffix($file->getLayout());
            $this->attachLayoutAndBlockContentToFileContent($file, $layout);

            $parameters = $this->createParameters($file, 'file');
            $content = $this->latteRenderer->renderFileWithParameters($file, $parameters);

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
            if (! in_array($file->getExtension(), ['latte', 'md'], true)) {
                continue;
            }

            if ($file->getExtension() === 'md' && $this->templatingDetector->detect() === 'twig') {
                continue;
            }

            $layout = $this->normalizeLayoutSuffix($file->getLayout() ?: $generatorElement->getLayout());
            $this->attachLayoutAndBlockContentToFileContent($file, $layout);

            $parameters = $this->createParameters($file, $generatorElement->getVariable());

            $content = $this->latteRenderer->renderFileWithParameters($file, $parameters);
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

    private function normalizeLayoutSuffix(?string $layout): ?string
    {
        if (Strings::endsWith($layout, '.twig')) {
            return Strings::replace($layout, '#\.twig$#', '.latte');
        }

        return $layout;
    }

    private function attachLayoutAndBlockContentToFileContent(AbstractFile $file, ?string $layout): void
    {
        if (! $layout) {
            return;
        }

        $content = $this->codeBlocksProtector->protectContentFromCallback(
            $file->getContent(),
            function (string $content) use ($layout) {
                if (! Strings::match($content, self::BLOCK_CONTENT_PATTERN)) {
                    $content = '{block content}' . $content . '{/block}';
                }

                if (! Strings::match($content, self::LAYOUT_PATTERN)) {
                    $content = '{layout "' . $layout . '"}' . $content;
                }

                return $content;
            }
        );

        $file->changeContent($content);
    }

    private function trimLayoutLeftover(string $content): string
    {
        return $this->codeBlocksProtector->protectContentFromCallback($content, function (string $content) {
            $content = Strings::replace($content, self::BLOCK_CONTENT_PATTERN, '$1', 1);

            return Strings::replace($content, self::LAYOUT_PATTERN, '', 1);
        });
    }
}
