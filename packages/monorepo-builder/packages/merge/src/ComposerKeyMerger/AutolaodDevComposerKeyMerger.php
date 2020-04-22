<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;

final class AutolaodDevComposerKeyMerger extends AbstractComposerKeyMerger implements ComposerKeyMergerInterface
{
    public function merge(ComposerJson $mainComposerJson, ComposerJson $newComposerJson): void
    {
        if ($newComposerJson->getAutoloadDev() === []) {
            return;
        }

        $autoloadDev = $this->mergeRecursiveAndSort(
            $mainComposerJson->getAutoloadDev(),
            $newComposerJson->getAutoloadDev()
        );

        $mainComposerJson->setAutoloadDev($autoloadDev);
    }
}
