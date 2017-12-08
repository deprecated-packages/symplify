<?php declare(strict_types=1);

namespace Symplify\Statie\Generator;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\FileSystem\FileFinder;
use Symplify\Statie\Generator\Configuration\GeneratorConfiguration;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
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
     * @var ObjectFactory
     */
    private $objectFactory;

    public function __construct(
        GeneratorConfiguration $generatorConfiguration,
        FileFinder $fileFinder,
        Configuration $configuration,
        RenderableFilesProcessor $renderableFilesProcessor,
        ObjectFactory $objectFactory
    ) {
        $this->generatorConfiguration = $generatorConfiguration;
        $this->fileFinder = $fileFinder;
        $this->configuration = $configuration;
        $this->renderableFilesProcessor = $renderableFilesProcessor;
        $this->objectFactory = $objectFactory;
    }

    public function run(): void
    {
        foreach ($this->generatorConfiguration->getGeneratorElements() as $generatorElement) {
            $this->processGeneratorElement($generatorElement);
        }
    }

    private function processGeneratorElement(GeneratorElement $generatorElement): void
    {
        if (! is_dir($generatorElement->getPath())) {
            return;
        }

        // find files in ...
        $fileInfos = $this->fileFinder->findInDirectory($generatorElement->getPath());
        if (! count($fileInfos)) {
            return;
        }

        // process to objects
        $objects = $this->objectFactory->createFromFileInfosAndGeneratorElement($fileInfos, $generatorElement);

        // save them to property
        $this->configuration->addOption($generatorElement->getVariableGlobal(), $objects);

        // run them through decorator and render them
        $this->renderableFilesProcessor->processGeneratorElementObjects($objects, $generatorElement);
    }
}
