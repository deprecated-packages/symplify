<?php declare(strict_types=1);

namespace Symplify\Statie\Application;

use SplFileInfo;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Event\BeforeRenderEvent;
use Symplify\Statie\FileSystem\FileFinder;
use Symplify\Statie\FileSystem\FileSystemWriter;
use Symplify\Statie\FlatWhite\Latte\DynamicStringLoader;
use Symplify\Statie\Generator\Generator;
use Symplify\Statie\Renderable\RenderableFilesProcessor;

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

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        Configuration $configuration,
        FileSystemWriter $fileSystemWriter,
        RenderableFilesProcessor $renderableFilesProcessor,
        DynamicStringLoader $dynamicStringLoader,
        Generator $generator,
        FileFinder $fileFinder,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->configuration = $configuration;
        $this->fileSystemWriter = $fileSystemWriter;
        $this->renderableFilesProcessor = $renderableFilesProcessor;
        $this->dynamicStringLoader = $dynamicStringLoader;
        $this->generator = $generator;
        $this->fileFinder = $fileFinder;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function run(string $source, string $destination, bool $dryRun = false): void
    {
        $this->configuration->setSourceDirectory($source);
        $this->configuration->setOutputDirectory($destination);
        $this->configuration->setDryRun($dryRun);

        // load layouts and snippets
        $layoutAndSnippetFiles = $this->fileFinder->findLatteLayoutsAndSnippets($source);
        $this->loadLayoutsToLatteLoader($layoutAndSnippetFiles);

        // process generator items
        $generatorFilesByType = $this->generator->run();

        // process rest of files (config call is due to absolute path)
        $fileInfos = $this->fileFinder->findRestOfRenderableFiles($this->configuration->getSourceDirectory());
        $files = $this->renderableFilesProcessor->processFileInfos($fileInfos);
//        $objectsToRender = array_merge($objectsToRender, $this->renderableFilesProcessor->processFileInfos($fileInfos));

        $this->eventDispatcher->dispatch(
            BeforeRenderEvent::class,
            new BeforeRenderEvent($files, $generatorFilesByType)
        );

        if ($dryRun === false) {
            // process static files
            $staticFiles = $this->fileFinder->findStaticFiles($source);
            $this->fileSystemWriter->copyStaticFiles($staticFiles);

            $this->fileSystemWriter->copyRenderableFiles($files);

            foreach ($generatorFilesByType as $generatorFiles) {
                $this->fileSystemWriter->copyRenderableFiles($generatorFiles);
            }
        }
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
