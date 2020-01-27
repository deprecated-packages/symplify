<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger;

use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;

final class AutolaodDevComposerKeyMerger extends AbstractComposerKeyMerger implements ComposerKeyMergerInterface
{
    public function merge(ComposerJson $rootComposerJson, ComposerJson $jsonToMerge): void
    {
        if ($jsonToMerge->getAutoloadDev() === []) {
            return;
        }

        $autoloadDev = $this->mergeRecursiveAndSort(
            $rootComposerJson->getAutoloadDev(),
            $jsonToMerge->getAutoloadDev()
        );
        $rootComposerJson->setAutoloadDev($autoloadDev);
    }
}
