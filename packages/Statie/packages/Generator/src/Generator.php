<?php declare(strict_types=1);

namespace Symplify\Statie\Generator;

use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\FileSystem\FileFinder;
use Symplify\Statie\Generator\Configuration\GeneratorConfiguration;
use Symplify\Statie\Generator\Configuration\GeneratorElement;

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

    public function __construct(
        GeneratorConfiguration $generatorConfiguration,
        FileFinder $fileFinder,
        Configuration $configuration
    ) {
        $this->generatorConfiguration = $generatorConfiguration;
        $this->fileFinder = $fileFinder;
        $this->configuration = $configuration;
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

        // run them throught decorator
        // @todo

        // render them
    }

    /**
     * @param SplFileInfo[] $fileInfos
     * @return object[]
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
