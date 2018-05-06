<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable;

use Symfony\Component\Finder\SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\FileFactory;

final class RenderableFilesProcessor
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var FileDecoratorInterface[]
     */
    private $fileDecorators = [];

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(FileFactory $fileFactory, Configuration $configuration)
    {
        $this->fileFactory = $fileFactory;
        $this->configuration = $configuration;
    }

    public function addFileDecorator(FileDecoratorInterface $fileDecorator): void
    {
        $this->fileDecorators[] = $fileDecorator;
    }

    /**
     * @param SplFileInfo[] $fileInfos
     * @return AbstractFile[]
     */
    public function processFileInfos(array $fileInfos): array
    {
        if (! count($fileInfos)) {
            return [];
        }

        $files = $this->fileFactory->createFromFileInfos($fileInfos);

        foreach ($this->fileDecorators as $fileDecorator) {
            $files = $fileDecorator->decorateFiles($files);
        }

        return $files;
    }

    /**
     * @param AbstractGeneratorFile[] $objects
     * @return AbstractGeneratorFile[]
     */
    public function processGeneratorElementObjects(array $objects, GeneratorElement $generatorElement): array
    {
        if (! count($objects)) {
            return [];
        }

        foreach ($this->fileDecorators as $fileDecorator) {
            $objects = $fileDecorator->decorateFilesWithGeneratorElement($objects, $generatorElement);
        }

        $objectSorter = $generatorElement->getObjectSorter();
        $objects = $objectSorter->sort($objects);

        $this->configuration->addOption($generatorElement->getVariableGlobal(), $objects);

        return $objects;
    }
}
