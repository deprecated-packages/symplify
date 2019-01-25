<?php declare(strict_types=1);

namespace Symplify\Statie\Application;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\Event\BeforeRenderEvent;
use Symplify\Statie\FileSystem\FileFinder;
use Symplify\Statie\FileSystem\FileSystemWriter;
use Symplify\Statie\Generator\Generator;
use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;
use Symplify\Statie\Generator\Renderable\File\GeneratorFile;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\VirtualFile;
use Symplify\Statie\Renderable\RedirectGenerator;
use Symplify\Statie\Renderable\RenderableFilesProcessor;
use Symplify\Statie\Templating\LayoutsAndSnippetsLoader;
use function Safe\sprintf;

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
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(
        StatieConfiguration $statieConfiguration,
        FileSystemWriter $fileSystemWriter,
        RenderableFilesProcessor $renderableFilesProcessor,
        Generator $generator,
        FileFinder $fileFinder,
        EventDispatcherInterface $eventDispatcher,
        LayoutsAndSnippetsLoader $layoutsAndSnippetsLoader,
        RedirectGenerator $redirectGenerator,
        SymfonyStyle $symfonyStyle
    ) {
        $this->statieConfiguration = $statieConfiguration;
        $this->fileSystemWriter = $fileSystemWriter;
        $this->renderableFilesProcessor = $renderableFilesProcessor;
        $this->generator = $generator;
        $this->fileFinder = $fileFinder;
        $this->eventDispatcher = $eventDispatcher;
        $this->layoutsAndSnippetsLoader = $layoutsAndSnippetsLoader;
        $this->redirectGenerator = $redirectGenerator;
        $this->symfonyStyle = $symfonyStyle;
    }

    public function run(string $source, string $destination, bool $dryRun = false): void
    {
        $this->statieConfiguration->setSourceDirectory($source);
        $this->statieConfiguration->setOutputDirectory($destination);
        $this->statieConfiguration->setDryRun($dryRun);

        // load layouts and snippets
        if ($this->symfonyStyle->isVerbose()) {
            $this->symfonyStyle->note(sprintf('Loading layouts and snippets from "%s"', $source));
        }
        $this->layoutsAndSnippetsLoader->loadFromSource($source);

        // process generator items
        if ($this->symfonyStyle->isVerbose()) {
            $this->symfonyStyle->note('Processing generator files');
        }
        $generatorFilesByType = $this->generator->run();
        if ($this->symfonyStyle->isVerbose()) {
            foreach ($generatorFilesByType as $type => $generatorFiles) {
                $this->symfonyStyle->note(sprintf('Generated %d %s', count($generatorFiles), $type));
            }
        }

        // process rest of files (config call is due to absolute path)
        $fileInfos = $this->fileFinder->findRestOfRenderableFiles($source);

        if ($this->symfonyStyle->isVerbose()) {
            $this->symfonyStyle->note(sprintf('Processing %d renderable files', count($fileInfos)));
        }
        $files = $this->renderableFilesProcessor->processFileInfos($fileInfos);

        $this->eventDispatcher->dispatch(
            BeforeRenderEvent::class,
            new BeforeRenderEvent($files, $generatorFilesByType)
        );

        $virtualFiles = $this->redirectGenerator->generate();
        if ($this->symfonyStyle->isVerbose()) {
            $this->symfonyStyle->note(sprintf('Generating %d virtual files', count($virtualFiles)));
        }

        if ($dryRun === false) {
            $this->renderFiles($source, $files, $virtualFiles, $generatorFilesByType);
        }
    }

    /**
     * @param AbstractFile[] $files
     * @param VirtualFile[] $virtualFiles
     * @param AbstractGeneratorFile[][] $generatorFilesByType
     */
    private function renderFiles(string $source, array $files, array $virtualFiles, array $generatorFilesByType): void
    {
        $staticFiles = $this->fileFinder->findStaticFiles($source);
        $this->fileSystemWriter->copyStaticFiles($staticFiles);

        $this->fileSystemWriter->renderFiles($files);
        $this->fileSystemWriter->renderFiles($virtualFiles);

        foreach ($generatorFilesByType as $generatorFiles) {
            $this->fileSystemWriter->renderFiles($generatorFiles);
        }
    }
}
