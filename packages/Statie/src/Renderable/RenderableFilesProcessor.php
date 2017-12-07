<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable;

use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\FileSystem\FileSystemWriter;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\FileFactory;

final class RenderableFilesProcessor
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var FileSystemWriter
     */
    private $fileSystemWriter;

    /**
     * @var FileDecoratorInterface[]
     */
    private $fileDecorators = [];

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        FileFactory $fileFactory,
        FileSystemWriter $fileSystemWriter,
        Configuration $configuration
    ) {
        $this->fileFactory = $fileFactory;
        $this->fileSystemWriter = $fileSystemWriter;
        $this->configuration = $configuration;
    }

    public function addFileDecorator(FileDecoratorInterface $fileDecorator): void
    {
        $this->fileDecorators[] = $fileDecorator;
    }

    /**
     * @param SplFileInfo[] $fileInfos
     */
    public function processFileInfos(array $fileInfos): void
    {
        if (! count($fileInfos)) {
            return;
        }

        $files = $this->fileFactory->createFromFileInfos($fileInfos);

        foreach ($this->fileDecorators as $fileDecorator) {
            $files = $fileDecorator->decorateFiles($files);
        }

        if ($this->configuration->isDryRun() === false) {
            $this->fileSystemWriter->copyRenderableFiles($files);
        }
    }

    /**
     * @param AbstractFile[] $objects
     */
    public function processGeneratorElementObjects(array $objects, GeneratorElement $generatorElement): void
    {
        if (! count($objects)) {
            return;
        }

        foreach ($this->fileDecorators as $fileDecorator) {
            $objects = $fileDecorator->decorateFilesWithGeneratorElement($objects, $generatorElement);
        }

        if ($this->configuration->isDryRun() === false) {
            $this->fileSystemWriter->copyRenderableFiles($objects);
        }
    }
}
