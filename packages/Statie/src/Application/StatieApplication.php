<?php declare(strict_types=1);

namespace Symplify\Statie\Application;

use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\FileSystem\FileFinder;
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
    /**
     * @var FileFinder
     */
    private $fileFinder;

    public function __construct(
        SourceFileStorage $sourceFileStorage,
        Configuration $configuration,
        FileSystemWriter $fileSystemWriter,
        RenderableFilesProcessor $renderableFilesProcessor,
        DynamicStringLoader $dynamicStringLoader,
        Generator $generator,
        FileFinder $fileFinder
    ) {
        $this->sourceFileStorage = $sourceFileStorage;
        $this->configuration = $configuration;
        $this->fileSystemWriter = $fileSystemWriter;
        $this->renderableFilesProcessor = $renderableFilesProcessor;
        $this->dynamicStringLoader = $dynamicStringLoader;
        $this->generator = $generator;
        $this->fileFinder = $fileFinder;
    }

    public function run(string $source, string $destination): void
    {
        $this->configuration->setSourceDirectory($source);
        $this->configuration->setOutputDirectory($destination);

        $this->loadSourcesFromDirectory($source);

        $this->fileSystemWriter->copyStaticFiles($this->fileFinder->findStaticFiles($source);

        $this->processTemplates();
    }

    private function loadSourcesFromDirectory(string $directory): void
    {
        $files = $this->fileFinder->findInDirectory($directory);
        $this->sourceFileStorage->loadSourcesFromFiles($files);
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
