<?php declare(strict_types=1);

namespace Symplify\Statie\Latte\Renderable;

use Latte\CompileException;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Exception\Renderable\File\AccessKeyNotAvailableException;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Latte\Exception\InvalidLatteSyntaxException;
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
            if (! in_array($file->getExtension(), ['latte', 'md'], true)) {
                continue;
            }

            $this->decorateFileWithGeneratorElements($file, $generatorElement);
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

    private function decorateFile(AbstractFile $file): void
    {
        $parameters = $file->getConfiguration() + $this->configuration->getOptions() + [
            'file' => $file,
        ];

        $htmlContent = $this->renderOuterWithLayout($file, $parameters);
        $file->changeContent($htmlContent);
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

        $htmlContent = $this->renderToString($file, $parameters);

        // trim {layout %s} left over
        $htmlContent = preg_replace('/{layout "[a-z]+"}/', '', $htmlContent);
        $file->changeContent($htmlContent);
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

        return $this->renderToString($file, $parameters);
    }

    /**
     * @param mixed[] $parameters
     */
    private function renderToString(AbstractFile $file, array $parameters): string
    {
        try {
            return $this->latteRenderer->render($file, $parameters);
        } catch (CompileException | AccessKeyNotAvailableException $exception) {
            throw new InvalidLatteSyntaxException(sprintf(
                'Invalid Latte syntax found or missing value in "%s" file: %s',
                $file->getFilePath(),
                $exception->getMessage()
            ));
        }
    }
}
