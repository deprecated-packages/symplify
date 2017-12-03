<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable;

use Latte\CompileException;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Exception\Latte\InvalidLatteSyntaxException;
use Symplify\Statie\FlatWhite\Latte\DynamicStringLoader;
use Symplify\Statie\FlatWhite\Latte\LatteRenderer;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\PostFile;

final class LatteFileDecorator implements FileDecoratorInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var DynamicStringLoader
     */
    private $dynamicStringLoader;

    /**
     * @var LatteRenderer
     */
    private $latteRenderer;

    public function __construct(
        Configuration $configuration,
        DynamicStringLoader $dynamicStringLoader,
        LatteRenderer $latteRenderer
    ) {
        $this->configuration = $configuration;
        $this->dynamicStringLoader = $dynamicStringLoader;
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
        dump($files, $generatorElement);
        die;
    }

    private function decorateFile(AbstractFile $file): void
    {
        $options = $this->configuration->getOptions();

        $parameters = $file->getConfiguration() + $options + [
            'posts' => $options['posts'] ?? [],
            'file' => $file,
        ];

        if ($file instanceof PostFile) {
            $parameters['post'] = $file;

            // add layout, "post" by default
            $layout = $file->getLayout() ?: 'post';
            $file->changeContent(sprintf('{layout "%s"}', $layout) . PHP_EOL . $file->getContent());
            $this->addTemplateToDynamicLatteStringLoader($file);

            $htmlContent = $this->renderToString($file, $parameters);

            // trim {layout %s} left over
            $htmlContent = preg_replace('/{layout "[a-z]+"}/', '', $htmlContent);

            $file->changeContent($htmlContent);
        } else {
            // normal file
            $htmlContent = $this->renderOuterWithLayout($file, $parameters);
            $file->changeContent($htmlContent);
        }
    }

    private function addTemplateToDynamicLatteStringLoader(AbstractFile $file): void
    {
        $this->dynamicStringLoader->changeContent(
            $file->getBaseName(),
            $file->getContent()
        );
    }

    private function prependLayoutToFileContent(AbstractFile $file): void
    {
        if (! $file->getLayout()) {
            return;
        }

        $layoutLine = sprintf('{layout "%s"}', $file->getLayout());
        $file->changeContent($layoutLine . PHP_EOL . PHP_EOL . $file->getContent());
    }

    /**
     * @param mixed[] $parameters
     */
    private function renderOuterWithLayout(AbstractFile $file, array $parameters): string
    {
        $this->prependLayoutToFileContent($file);
        $this->addTemplateToDynamicLatteStringLoader($file);

        return $this->renderToString($file, $parameters);
    }

    /**
     * @param mixed[] $parameters
     */
    private function renderToString(AbstractFile $file, array $parameters): string
    {
        try {
            return $this->latteRenderer->renderExcludingHighlightBlocks($file->getBaseName(), $parameters);
        } catch (CompileException $latteCompileException) {
            throw new InvalidLatteSyntaxException(sprintf(
                'Invalid Latte syntax found in "%s" file: %s',
                $file->getFilePath(),
                $latteCompileException->getMessage()
            ));
        }
    }
}
