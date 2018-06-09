<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder;

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
     * @param string[] $mergeSections
     */
    public function __construct(array $mergeSections, JsonFileManager $jsonFileManager)
    {
        $this->mergeSections = $mergeSections;
        $this->jsonFileManager = $jsonFileManager;
    }

    public function addComposerJsonDecorator(ComposerJsonDecoratorInterface $composerJsonDecorator): void
    {
        $this->composerJsonDecorators[] = $composerJsonDecorator;
    }

    /**
     * @param mixed[] $jsonToMerge
     */
    public function mergeJsonToRootFilePath(array $jsonToMerge, string $rootFilePath): void
    {
        $rootComposerJson = $this->jsonFileManager->loadFromFilePath($rootFilePath);

        foreach ($this->mergeSections as $sectionToMerge) {
            // nothing collected to merge
            if (! isset($jsonToMerge[$sectionToMerge]) || empty($jsonToMerge[$sectionToMerge])) {
                continue;
            }

            // section in root composer.json is empty, just set and go
            if (! isset($rootComposerJson[$sectionToMerge])) {
                $rootComposerJson[$sectionToMerge] = $jsonToMerge[$sectionToMerge];
                break;
            }

            $rootComposerJson[$sectionToMerge] = $jsonToMerge[$sectionToMerge];
        }

        foreach ($this->composerJsonDecorators as $composerJsonDecorator) {
            $rootComposerJson = $composerJsonDecorator->decorate($rootComposerJson);
        }

        $this->jsonFileManager->saveJsonWithFilePath($rootComposerJson, $rootFilePath);
    }
}
