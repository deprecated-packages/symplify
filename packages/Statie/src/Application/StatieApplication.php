<?php declare(strict_types=1);

namespace Symplify\Statie\Application;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Event\BeforeRenderEvent;
use Symplify\Statie\FileSystem\FileFinder;
use Symplify\Statie\FileSystem\FileSystemWriter;
use Symplify\Statie\Generator\Generator;
use Symplify\Statie\Renderable\RenderableFilesProcessor;
use Symplify\Statie\Templating\LayoutsAndSnippetsLoader;

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

    /**
     * @var LayoutsAndSnippetsLoader
     */
    private $layoutsAndSnippetsLoader;

    public function __construct(
        Configuration $configuration,
        FileSystemWriter $fileSystemWriter,
        RenderableFilesProcessor $renderableFilesProcessor,
        Generator $generator,
        FileFinder $fileFinder,
        EventDispatcherInterface $eventDispatcher,
        LayoutsAndSnippetsLoader $layoutsAndSnippetsLoader
    ) {
        $this->configuration = $configuration;
        $this->fileSystemWriter = $fileSystemWriter;
        $this->renderableFilesProcessor = $renderableFilesProcessor;
        $this->generator = $generator;
        $this->fileFinder = $fileFinder;
        $this->eventDispatcher = $eventDispatcher;
        $this->layoutsAndSnippetsLoader = $layoutsAndSnippetsLoader;
    }

    public function run(string $source, string $destination, bool $dryRun = false): void
    {
        $this->configuration->setSourceDirectory($source);
        $this->configuration->setOutputDirectory($destination);
        $this->configuration->setDryRun($dryRun);

        // load layouts and snippets
        $this->layoutsAndSnippetsLoader->loadFromSource($source);

        // process generator items
        $generatorFilesByType = $this->generator->run();

        // process rest of files (config call is due to absolute path)
        $fileInfos = $this->fileFinder->findRestOfRenderableFiles($this->configuration->getSourceDirectory());
        $files = $this->renderableFilesProcessor->processFileInfos($fileInfos);

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
}
