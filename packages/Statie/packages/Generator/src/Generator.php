<?php declare(strict_types=1);

namespace Symplify\Statie\Generator;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\FileSystem\FileFinder;
use Symplify\Statie\Generator\Configuration\GeneratorConfiguration;
use Symplify\Statie\Generator\Renderable\File\GeneratorFileFactory;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\RenderableFilesProcessor;

final class Generator
{
    /**
     * @var GeneratorConfiguration
     */
    private $generatorConfiguration;

    /**
     * @var FileFinder
     */
    private $fileFinder;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var RenderableFilesProcessor
     */
    private $renderableFilesProcessor;

    /**
     * @var GeneratorFileFactory
     */
    private $generatorFileFactory;

    public function __construct(
        GeneratorConfiguration $generatorConfiguration,
        FileFinder $fileFinder,
        Configuration $configuration,
        RenderableFilesProcessor $renderableFilesProcessor,
        GeneratorFileFactory $generatorFileFactory
    ) {
        $this->generatorConfiguration = $generatorConfiguration;
        $this->fileFinder = $fileFinder;
        $this->configuration = $configuration;
        $this->renderableFilesProcessor = $renderableFilesProcessor;
        $this->generatorFileFactory = $generatorFileFactory;
    }

    /**
     * @return AbstractFile[]
     */
    public function run(): array
    {
        // configure
        foreach ($this->generatorConfiguration->getGeneratorElements() as $generatorElement) {
            if (! is_dir($generatorElement->getPath())) {
                continue;
            }

            // find files in ...
            $fileInfos = $this->fileFinder->findInDirectoryForGenerator($generatorElement->getPath());
            if (! count($fileInfos)) {
                continue;
            }

            // process to objects
            $objects = $this->generatorFileFactory->createFromFileInfosAndClass(
                $fileInfos,
                $generatorElement->getObject()
            );

            // save them to property (for "related_items" option)
            $this->configuration->addOption($generatorElement->getVariableGlobal(), $objects);

            $generatorElement->setObjects($objects);
        }

        $processedObjects = [];
        foreach ($this->generatorConfiguration->getGeneratorElements() as $generatorElement) {
            // run them through decorator and render content to string
            $newObjects = $this->renderableFilesProcessor->processGeneratorElementObjects(
                $generatorElement->getObjects(),
                $generatorElement
            );

            $processedObjects = array_merge($processedObjects, $newObjects);
        }

        return $processedObjects;
    }
}
