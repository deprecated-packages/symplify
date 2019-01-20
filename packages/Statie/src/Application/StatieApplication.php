<?php declare(strict_types=1);

namespace Symplify\Statie\Application;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\Event\BeforeRenderEvent;
use Symplify\Statie\FileSystem\FileFinder;
use Symplify\Statie\FileSystem\FileSystemWriter;
use Symplify\Statie\Generator\Generator;
use Symplify\Statie\Renderable\ApiGenerator;
use Symplify\Statie\Renderable\RedirectGenerator;
use Symplify\Statie\Renderable\RenderableFilesProcessor;
use Symplify\Statie\Templating\LayoutsAndSnippetsLoader;

final class StatieApplication
{
    /**
     * @var StatieConfiguration
     */
    private $statieConfiguration;

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

    /**
     * @var RedirectGenerator
     */
    private $redirectGenerator;

    /**
     * @var ApiGenerator
     */
    private $apiGenerator;

    public function __construct(
        StatieConfiguration $statieConfiguration,
        FileSystemWriter $fileSystemWriter,
        RenderableFilesProcessor $renderableFilesProcessor,
        Generator $generator,
        FileFinder $fileFinder,
        EventDispatcherInterface $eventDispatcher,
        LayoutsAndSnippetsLoader $layoutsAndSnippetsLoader,
        RedirectGenerator $redirectGenerator,
        ApiGenerator $apiGenerator
    ) {
        $this->statieConfiguration = $statieConfiguration;
        $this->fileSystemWriter = $fileSystemWriter;
        $this->renderableFilesProcessor = $renderableFilesProcessor;
        $this->generator = $generator;
        $this->fileFinder = $fileFinder;
        $this->eventDispatcher = $eventDispatcher;
        $this->layoutsAndSnippetsLoader = $layoutsAndSnippetsLoader;
        $this->redirectGenerator = $redirectGenerator;
        $this->apiGenerator = $apiGenerator;
    }

    public function run(string $source, string $destination, bool $dryRun = false): void
    {
        $this->statieConfiguration->setSourceDirectory($source);
        $this->statieConfiguration->setOutputDirectory($destination);
        $this->statieConfiguration->setDryRun($dryRun);

        // load layouts and snippets
        $this->layoutsAndSnippetsLoader->loadFromSource($source);

        // process generator items
        $generatorFilesByType = $this->generator->run();

        // process rest of files (config call is due to absolute path)
        $fileInfos = $this->fileFinder->findRestOfRenderableFiles($this->statieConfiguration->getSourceDirectory());
        $files = $this->renderableFilesProcessor->processFileInfos($fileInfos);

        $this->eventDispatcher->dispatch(
            BeforeRenderEvent::class,
            new BeforeRenderEvent($files, $generatorFilesByType)
        );

        $redirectFiles = $this->redirectGenerator->generate();
        $apiFiles = $this->apiGenerator->generate();

        if ($dryRun === false) {
            // process static files
            $staticFiles = $this->fileFinder->findStaticFiles($source);
            $this->fileSystemWriter->copyStaticFiles($staticFiles);

            $this->fileSystemWriter->renderFiles($files);
            $this->fileSystemWriter->renderFiles($redirectFiles);
            $this->fileSystemWriter->renderFiles($apiFiles);

            foreach ($generatorFilesByType as $generatorFiles) {
                $this->fileSystemWriter->renderFiles($generatorFiles);
            }
        }
    }
}
