<?php declare(strict_types=1);

namespace Symplify\Statie\Generator;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\FileSystem\FileFinder;
use Symplify\Statie\Generator\Configuration\GeneratorConfiguration;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Generator\Exception\Configuration\GeneratorException;
use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;
use Symplify\Statie\Generator\Renderable\File\GeneratorFileFactory;
use Symplify\Statie\Renderable\RenderableFilesProcessor;
use function Safe\sprintf;

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
     * @return AbstractGeneratorFile[][]
     */
    public function run(): array
    {
        foreach ($this->generatorConfiguration->getGeneratorElements() as $generatorElement) {
            if (! is_dir($generatorElement->getPath())) {
                $this->reportMissingPath($generatorElement);

                continue;
            }

            $objects = $this->createObjectsFromFoundElements($generatorElement);

            // save them to property (for "related_items" option)
            $this->configuration->addOption($generatorElement->getVariableGlobal(), $objects);

            $generatorElement->setObjects($objects);
        }

        $generatorFilesByType = [];
        foreach ($this->generatorConfiguration->getGeneratorElements() as $generatorElement) {
            $key = $generatorElement->getVariableGlobal();
            if (isset($generatorFilesByType[$key])) {
                throw new GeneratorException(sprintf(
                    'Generator element for "%s" global variable already exists.',
                    $key
                ));
            }

            // run them through decorator and render content to string
            $generatorFilesByType[$key] = $this->renderableFilesProcessor->processGeneratorElementObjects(
                $generatorElement->getObjects(),
                $generatorElement
            );
        }

        return $generatorFilesByType;
    }

    private function reportMissingPath(GeneratorElement $generatorElement): void
    {
        if ($generatorElement->getVariableGlobal() !== 'posts') {
            throw new GeneratorException(sprintf(
                'Path "%s" for generated element "%s" was not found.',
                $generatorElement->getPath(),
                $generatorElement->getVariableGlobal()
            ));
        }
    }

    /**
     * @return AbstractGeneratorFile[]
     */
    private function createObjectsFromFoundElements(GeneratorElement $generatorElement): array
    {
        $fileInfos = $this->fileFinder->findInDirectoryForGenerator($generatorElement->getPath());
        if (count($fileInfos) === 0) {
            return [];
        }

        // process to objects
        return $this->generatorFileFactory->createFromFileInfosAndClass($fileInfos, $generatorElement->getObject());
    }
}
