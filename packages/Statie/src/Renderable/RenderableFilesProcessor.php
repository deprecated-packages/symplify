<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable;

use SplFileInfo;
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

    public function __construct(FileFactory $fileFactory, FileSystemWriter $fileSystemWriter)
    {
        $this->fileFactory = $fileFactory;
        $this->fileSystemWriter = $fileSystemWriter;
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

        $this->fileSystemWriter->copyRenderableFiles($files);
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

        $this->fileSystemWriter->copyRenderableFiles($objects);
    }
}
