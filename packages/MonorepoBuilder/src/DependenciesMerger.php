<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder;

use Symplify\MonorepoBuilder\Configuration\MergedPackagesCollector;
use Symplify\MonorepoBuilder\Contract\ComposerJsonDecoratorInterface;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;

final class DependenciesMerger
{
    /**
     * @var string[]
     */
    private $mergeSections = [];

    /**
     * @var ComposerJsonDecoratorInterface[]
     */
    private $composerJsonDecorators = [];

    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    /**
     * @var MergedPackagesCollector
     */
    private $mergedPackagesCollector;

    /**
     * @param string[] $mergeSections
     */
    public function __construct(
        array $mergeSections,
        JsonFileManager $jsonFileManager,
        MergedPackagesCollector $mergedPackagesCollector
    ) {
        $this->mergeSections = $mergeSections;
        $this->jsonFileManager = $jsonFileManager;
        $this->mergedPackagesCollector = $mergedPackagesCollector;
    }

    public function addComposerJsonDecorator(ComposerJsonDecoratorInterface $composerJsonDecorator): void
    {
        $this->composerJsonDecorators[] = $composerJsonDecorator;
    }

    /**
     * @param mixed[] $jsonToMerge
     * @return mixed[]
     */
    public function mergeJsonToRootFilePath(array $jsonToMerge, string $rootFilePath): array
    {
        $rootComposerJson = $this->jsonFileManager->loadFromFilePath($rootFilePath);

        if (isset($jsonToMerge['name'])) {
            $this->mergedPackagesCollector->addPackage($jsonToMerge['name']);
        }

        foreach ($this->mergeSections as $sectionToMerge) {
            // nothing collected to merge
            if (! isset($jsonToMerge[$sectionToMerge]) || empty($jsonToMerge[$sectionToMerge])) {
                continue;
            }

            // section in root composer.json is empty, just set and go
            $rootComposerJson[$sectionToMerge] = $jsonToMerge[$sectionToMerge];
        }

        foreach ($this->composerJsonDecorators as $composerJsonDecorator) {
            $rootComposerJson = $composerJsonDecorator->decorate($rootComposerJson);
        }

        return $rootComposerJson;
    }

    /**
     * @param mixed[] $jsonToMerge
     */
    public function mergeJsonToRootFilePathAndSave(array $jsonToMerge, string $rootFilePath): void
    {
        $mergedJson = $this->mergeJsonToRootFilePath($jsonToMerge, $rootFilePath);

        $this->jsonFileManager->saveJsonWithFilePath($mergedJson, $rootFilePath);
    }
}
