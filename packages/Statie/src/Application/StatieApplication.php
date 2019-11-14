<?php declare(strict_types=1);

namespace Symplify\Statie\Application;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\FileSystem\FileFinder;
use Symplify\Statie\FileSystem\FileSystemWriter;
use Symplify\Statie\Generator\Generator;
use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;
use Symplify\Statie\Renderable\ApiGenerator;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\VirtualFile;
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
        LayoutsAndSnippetsLoader $layoutsAndSnippetsLoader,
        RedirectGenerator $redirectGenerator,
        SymfonyStyle $symfonyStyle,
        ApiGenerator $apiGenerator
    ) {
        $this->statieConfiguration = $statieConfiguration;
        $this->fileSystemWriter = $fileSystemWriter;
        $this->renderableFilesProcessor = $renderableFilesProcessor;
        $this->generator = $generator;
        $this->fileFinder = $fileFinder;
        $this->layoutsAndSnippetsLoader = $layoutsAndSnippetsLoader;
        $this->redirectGenerator = $redirectGenerator;
        $this->symfonyStyle = $symfonyStyle;
        $this->apiGenerator = $apiGenerator;
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
        $this->reportGeneratorFiles($generatorFilesByType);

        // process rest of files (config call is due to absolute path)
        $fileInfos = $this->fileFinder->findRestOfRenderableFiles($source);
        $fileInfos = $this->filterOutGeneratorFiles($fileInfos, $generatorFilesByType);

        $this->reportRenderableFiles($fileInfos);

        $files = $this->renderableFilesProcessor->processFileInfos($fileInfos);

        $redirectFiles = $this->redirectGenerator->generate();
        $this->reportRedirectFiles($redirectFiles);

        $apiFiles = $this->apiGenerator->generate();
        $this->reportApiFiles($apiFiles);

        if (! $dryRun) {
            $virtualFiles = array_merge($redirectFiles, $apiFiles);
            $this->renderFiles($source, $files, $virtualFiles, $generatorFilesByType);
        }
    }

    /**
     * @param AbstractGeneratorFile[][] $generatorFilesByType
     */
    private function reportGeneratorFiles(array $generatorFilesByType): void
    {
        if (! $this->symfonyStyle->isVerbose()) {
            return;
        }

        foreach ($generatorFilesByType as $type => $generatorFiles) {
            if (count($generatorFiles) === 0) {
                continue;
            }

            $this->symfonyStyle->note(sprintf('Generated %d %s', count($generatorFiles), $type));
        }
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @param AbstractGeneratorFile[][] $generatorFilesByType
     * @return SmartFileInfo[]
     */
    private function filterOutGeneratorFiles(array $fileInfos, array $generatorFilesByType)
    {
        return array_filter($fileInfos, function (SmartFileInfo $fileInfo) use ($generatorFilesByType): bool {
            return ! $this->isFilePartOfGeneratorsFiles($fileInfo, $generatorFilesByType);
        });
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     */
    private function reportRenderableFiles(array $fileInfos): void
    {
        if ($this->symfonyStyle->isVerbose() && count($fileInfos)) {
            $this->symfonyStyle->note(sprintf('Processing %d renderable files', count($fileInfos)));
        }
    }

    /**
     * @param VirtualFile[] $redirectFiles
     */
    private function reportRedirectFiles(array $redirectFiles): void
    {
        if ($this->symfonyStyle->isVerbose() && count($redirectFiles)) {
            $this->symfonyStyle->note(sprintf('Generating %d redirect files', count($redirectFiles)));
        }
    }

    /**
     * @param VirtualFile[] $apiFiles
     */
    private function reportApiFiles(array $apiFiles): void
    {
        if ($this->symfonyStyle->isVerbose() && count($apiFiles)) {
            $this->symfonyStyle->note(sprintf('Generating %d api files', count($apiFiles)));
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

    /**
     * @param AbstractGeneratorFile[][] $generatorFilesByType
     */
    private function isFilePartOfGeneratorsFiles(SmartFileInfo $fileInfo, array $generatorFilesByType): bool
    {
        foreach ($generatorFilesByType as $generatorFiles) {
            foreach ($generatorFiles as $generatorFile) {
                if ($fileInfo->getRealPath() === $generatorFile->getFileInfo()->getRealPath()) {
                    return true;
                }
            }
        }

        return false;
    }
}
