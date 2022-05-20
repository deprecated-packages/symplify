<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Arrays\SortedParameterMerger;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;

final class ProvideComposerKeyMerger implements ComposerKeyMergerInterface
{
    public function __construct(
        private SortedParameterMerger $sortedParameterMerger
    ) {
    }

    public function merge(ComposerJson $mainComposerJson, ComposerJson $newComposerJson): void
    {
        if ($newComposerJson->getProvide() === []) {
            return;
        }

        $provide = $this->sortedParameterMerger->mergeRecursiveAndSort(
            $this->explodeVersions($newComposerJson->getProvide()),
            $this->explodeVersions($mainComposerJson->getProvide())
        );

        $mainComposerJson->setProvide($this->mergeVersions($provide));
    }

    /**
     * @param array<string, string> $provide
     *
     * @return array<string, array<int, string>>
     */
    private function explodeVersions(array $provide): array
    {
        $explodedProvide = [];

        foreach ($provide as $packageName => $versionString) {
            $versions = explode('|', $versionString);
            foreach ($versions as $version) {
                $explodedProvide[$packageName][] = \trim($version);
            }
        }

        return $explodedProvide;
    }

    /**
     * @param array<string, array<int, string>> $provide
     *
     * @return array<string, string>
     */
    private function mergeVersions(array $provide): array
    {
        $mergedProvide = [];

        foreach ($provide as $packageName => $versions) {
            if (\in_array('*', $versions, true)) {
                $mergedProvide[$packageName] = '*';
            } else {
                $uniqueVersions = array_unique($versions);
                $mergedProvide[$packageName] = implode('|', $uniqueVersions);
            }
        }

        return $mergedProvide;
    }
}
