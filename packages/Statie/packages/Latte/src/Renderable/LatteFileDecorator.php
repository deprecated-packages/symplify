<?php declare(strict_types=1);

namespace Symplify\Statie\Latte\Renderable;

use Nette\Utils\Strings;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Latte\LatteRenderer;
use Symplify\Statie\Renderable\CodeBlocksProtector;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Templating\AbstractTemplatingFileDecorator;

final class LatteFileDecorator extends AbstractTemplatingFileDecorator implements FileDecoratorInterface
{
    /**
     * @var LatteRenderer
     */
    private $latteRenderer;

    /**
     * @var CodeBlocksProtector
     */
    private $codeBlocksProtector;

    public function __construct(LatteRenderer $latteRenderer, CodeBlocksProtector $codeBlocksProtector)
    {
        $this->latteRenderer = $latteRenderer;
        $this->codeBlocksProtector = $codeBlocksProtector;
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

            $this->prependLayoutToFileContent($file, $file->getLayout());

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

            $this->prependLayoutToFileContent($file, $generatorElement->getLayout());

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

    private function prependLayoutToFileContent(AbstractFile $file, ?string $layout): void
    {
        if (! $layout) {
            return;
        }

        $content = $this->codeBlocksProtector->protectContentFromCallback(
            $file->getContent(),
            function (string $content) use ($layout) {
                return $this->wrapWithLayoutAndBlockContent($content, $layout);
            }
        );

        $file->changeContent($content);
    }

    private function trimLayoutLeftover(string $content): string
    {
        return $this->codeBlocksProtector->protectContentFromCallback($content, function (string $content) {
            $content = Strings::replace($content, '#{block content}(.*){/block}#s', '$1', 1);

            return Strings::replace($content, '#{layout (.*?)}#', '', 1);
        });
    }

    private function wrapWithLayoutAndBlockContent(string $content, string $layout): string
    {
        if (! Strings::match($content, '#{block content}(.*){/block}#s')) {
            $content = '{block content}' . $content . '{/block}';
        }

        if (! Strings::match($content, '#{layout (.*?)}#')) {
            $content = sprintf('{layout "%s"}', $layout) . PHP_EOL . $content;
        }

        return $content;
    }
}
