<?php declare(strict_types=1);

namespace Symplify\Statie\Application;

use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\FileSystem\FileFinder;
use Symplify\Statie\FlatWhite\Latte\DynamicStringLoader;
use Symplify\Statie\Generator\Generator;
use Symplify\Statie\FileSystem\FileSystemWriter;
use Symplify\Statie\Renderable\RenderableFilesProcessor;
use Symplify\Statie\Source\SourceFileStorage;

final class StatieApplication
{
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
        Configuration $configuration,
        FileSystemWriter $fileSystemWriter,
        RenderableFilesProcessor $renderableFilesProcessor,
        DynamicStringLoader $dynamicStringLoader,
        Generator $generator,
        FileFinder $fileFinder
    ) {
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

        // load layouts and snippets
        $layoutAndSnippetFiles = $this->fileFinder->findLatteLayoutsAndSnippets($source);
        $this->loadLayoutsToLatteLoader($layoutAndSnippetFiles);

        // process static files
        $staticFiles = $this->fileFinder->findStaticFiles($source);
        $this->fileSystemWriter->copyStaticFiles($staticFiles);

        // process generator items
        $this->generator->run();

        // render rest of files
        $restOfRenderableFiles = $this->fileFinder->getRestOfRenderableFiles($source);
        $this->renderableFilesProcessor->processFileInfos($restOfRenderableFiles);
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
}
