<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger;

use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;

final class RepositoriesComposerKeyMerger extends AbstractComposerKeyMerger implements ComposerKeyMergerInterface
{
    public function merge(ComposerJson $rootComposerJson, ComposerJson $jsonToMerge): void
    {
        if ($jsonToMerge->getRepositories() === []) {
            return;
        }

        $repositories = $this->mergeRecursiveAndSort(
            $rootComposerJson->getRepositories(),
            $jsonToMerge->getRepositories()
        );

        // uniquate special cases, ref https://github.com/symplify/symplify/issues/1197
        $repositories = array_unique($repositories, SORT_REGULAR);
        // remove keys
        $repositories = array_values($repositories);

        $rootComposerJson->setRepositories($repositories);
    }
}
