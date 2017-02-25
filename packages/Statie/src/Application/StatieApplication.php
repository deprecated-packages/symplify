<?php declare(strict_types=1);

namespace Symplify\Statie\Application;

use Nette\Utils\Finder;
use SplFileInfo;
use Symplify\Statie\Application\Command\RunCommand;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Output\FileSystemWriter;
use Symplify\Statie\Renderable\Latte\DynamicStringLoader;
use Symplify\Statie\Renderable\RenderableFilesProcessor;
use Symplify\Statie\Source\SourceFileStorage;
use Symplify\Statie\Utils\FilesystemChecker;

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

    public function __construct(
        SourceFileStorage $sourceFileStorage,
        Configuration $configuration,
        FileSystemWriter $fileSystemWriter,
        RenderableFilesProcessor $renderableFilesProcessor,
        DynamicStringLoader $dynamicStringLoader
    ) {
        $this->sourceFileStorage = $sourceFileStorage;
        $this->configuration = $configuration;
        $this->fileSystemWriter = $fileSystemWriter;
        $this->renderableFilesProcessor = $renderableFilesProcessor;
        $this->dynamicStringLoader = $dynamicStringLoader;
    }

    public function runCommand(RunCommand $runCommand): void
    {
        $this->loadConfigurationWithDirectories($runCommand);

        FilesystemChecker::ensureDirectoryExists($runCommand->getSourceDirectory());

        $this->loadSourcesFromSourceDirectory($runCommand->getSourceDirectory());

        // 1. copy static files
        $this->fileSystemWriter->copyStaticFiles($this->sourceFileStorage->getStaticFiles());

        // 2. collect configuration
        $this->configuration->loadFromFiles($this->sourceFileStorage->getConfigurationFiles());

        // 3. collect layouts
        $this->loadLayoutsToLatteLoader($this->sourceFileStorage->getLayoutFiles());

        // 4. completely process post
        $this->renderableFilesProcessor->processFiles($this->sourceFileStorage->getPostFiles());

        // 5. render files
        $this->renderableFilesProcessor->processFiles($this->sourceFileStorage->getRenderableFiles());
    }

    private function loadConfigurationWithDirectories(RunCommand $runCommand): void
    {
        $this->configuration->setSourceDirectory($runCommand->getSourceDirectory());
        $this->configuration->setOutputDirectory($runCommand->getOutputDirectory());
    }

    private function loadSourcesFromSourceDirectory(string $sourceDirectory): void
    {
        $files = $this->findFilesInSourceDirectory($sourceDirectory);
        $this->sourceFileStorage->loadSourcesFromFiles($files);
    }

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
            $this->dynamicStringLoader->addTemplate($name, $content);
        }
    }
}
