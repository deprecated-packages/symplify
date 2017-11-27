<?php declare(strict_types=1);

namespace Symplify\Statie\Application;

use Nette\Utils\Finder;
use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\FlatWhite\Latte\DynamicStringLoader;
use Symplify\Statie\Generator\Generator;
use Symplify\Statie\Output\FileSystemWriter;
use Symplify\Statie\Renderable\RenderableFilesProcessor;
use Symplify\Statie\Source\SourceFileStorage;

final class StatieApplication
{
    /**
     * @var SourceFileStorage
     */
    private $sourceFileStorage;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var FileSystemWriter
     */
    private $fileSystemWriter;

    /**
     * @var RenderableFilesProcessor
     */
    private $renderableFilesProcessor;

    /**
     * @var DynamicStringLoader
     */
    private $dynamicStringLoader;

    /**
     * @var Generator
     */
    private $generator;

    public function __construct(
        SourceFileStorage $sourceFileStorage,
        Configuration $configuration,
        FileSystemWriter $fileSystemWriter,
        RenderableFilesProcessor $renderableFilesProcessor,
        DynamicStringLoader $dynamicStringLoader,
        Generator $generator
    ) {
        $this->sourceFileStorage = $sourceFileStorage;
        $this->configuration = $configuration;
        $this->fileSystemWriter = $fileSystemWriter;
        $this->renderableFilesProcessor = $renderableFilesProcessor;
        $this->dynamicStringLoader = $dynamicStringLoader;
        $this->generator = $generator;
    }

    public function run(string $source, string $destination): void
    {
        $this->configuration->setSourceDirectory($source);
        $this->configuration->setOutputDirectory($destination);

        $this->loadSourcesFromSourceDirectory($source);

        $this->fileSystemWriter->copyStaticFiles($this->sourceFileStorage->getStaticFiles());

        $this->processTemplates();
    }

    private function loadSourcesFromSourceDirectory(string $sourceDirectory): void
    {
        $files = $this->findFilesInSourceDirectory($sourceDirectory);
        $this->sourceFileStorage->loadSourcesFromFiles($files);
    }

    /**
     * @return SplFileInfo[]
     */
    private function findFilesInSourceDirectory(string $sourceDirectory): array
    {
        $finder = Finder::findFiles('*')->from($sourceDirectory);

        $files = [];
        foreach ($finder as $key => $file) {
            $files[$key] = $file;
        }

        return $files;
    }

    /**
     * @param SplFileInfo[] $layoutFiles
     */
    private function loadLayoutsToLatteLoader(array $layoutFiles): void
    {
        foreach ($layoutFiles as $layoutFile) {
            $name = $layoutFile->getBasename('.' . $layoutFile->getExtension());
            $content = file_get_contents($layoutFile->getRealPath());
            $this->dynamicStringLoader->changeContent($name, $content);
        }
    }

    private function processTemplates(): void
    {
        // 1. collect layouts
        $this->loadLayoutsToLatteLoader($this->sourceFileStorage->getLayoutFiles());

        // 2. process posts
        $this->generator->run();

        // 3. render files
        $this->renderableFilesProcessor->processFiles($this->sourceFileStorage->getRenderableFiles());
    }
}
