<?php declare(strict_types=1);

namespace Symplify\Statie\Generator;

use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\FileSystem\FileFinder;
use Symplify\Statie\Generator\Configuration\GeneratorConfiguration;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
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

    public function __construct(
        GeneratorConfiguration $generatorConfiguration,
        FileFinder $fileFinder,
        Configuration $configuration,
        RenderableFilesProcessor $renderableFilesProcessor
    ) {
        $this->generatorConfiguration = $generatorConfiguration;
        $this->fileFinder = $fileFinder;
        $this->configuration = $configuration;
        $this->renderableFilesProcessor = $renderableFilesProcessor;
    }

    public function run(): void
    {
        foreach ($this->generatorConfiguration->getGeneratorElements() as $generatorElement) {
            $this->processGeneratorElement($generatorElement);
        }
    }

    private function processGeneratorElement(GeneratorElement $generatorElement): void
    {
        // find files in...
        $fileInfos = $this->fileFinder->findInDirectory($generatorElement->getPath());
        if (! count($fileInfos)) {
            return;
        }

        // process to objects
        $objects = $this->createObjectsFromFileInfos($generatorElement, $fileInfos);

        // save them to property
        $this->configuration->addOption($generatorElement->getVariable(), $objects);

        // run them through decorator and render them
        $this->renderableFilesProcessor->processGeneratorElementObjects($objects, $generatorElement);
    }

    /**
     * @param SplFileInfo[] $fileInfos
     * @return AbstractFile[]
     */
    private function createObjectsFromFileInfos(GeneratorElement $generatorElement, array $fileInfos): array
    {
        $objects = [];

        foreach ($fileInfos as $fileInfo) {
            $relativeSource = substr($fileInfo->getPathname(), strlen($this->configuration->getSourceDirectory()));
            $relativeSource = ltrim($relativeSource, DIRECTORY_SEPARATOR);

            $class = $generatorElement->getObject();

            $objects[] = new $class($fileInfo, $relativeSource, $fileInfo->getPathname());
        }

        return $objects;
    }
}
