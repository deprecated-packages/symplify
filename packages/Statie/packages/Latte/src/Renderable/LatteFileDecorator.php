<?php declare(strict_types=1);

namespace Symplify\Statie\Latte\Renderable;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Latte\LatteRenderer;
use Symplify\Statie\Renderable\File\AbstractFile;

final class LatteFileDecorator implements FileDecoratorInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var LatteRenderer
     */
    private $latteRenderer;

    public function __construct(Configuration $configuration, LatteRenderer $latteRenderer)
    {
        $this->configuration = $configuration;
        $this->latteRenderer = $latteRenderer;
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

            $parameters = $file->getConfiguration() + $this->configuration->getOptions() + [
                'file' => $file,
            ];

            $htmlContent = $this->renderOuterWithLayout($file, $parameters);
            $file->changeContent($htmlContent);
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

            $parameters = $file->getConfiguration() + $this->configuration->getOptions() + [
                $generatorElement->getVariable() => $file,
            ];

            $this->prependLayoutToFileContent($file, $generatorElement->getLayout());

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

    private function prependLayoutToFileContent(AbstractFile $file, string $layout): void
    {
        $file->changeContent(sprintf('{layout "%s"}', $layout) . PHP_EOL . $file->getContent());
    }

    /**
     * @param mixed[] $parameters
     */
    private function renderOuterWithLayout(AbstractFile $file, array $parameters): string
    {
        if ($file->getLayout()) {
            $this->prependLayoutToFileContent($file, $file->getLayout());
        }

        return $this->latteRenderer->renderFileWithParameters($file, $parameters);
    }

    private function trimLayoutLeftover(string $content): string
    {
        return preg_replace('#{layout [^}]+}#', '', $content);
    }
}
