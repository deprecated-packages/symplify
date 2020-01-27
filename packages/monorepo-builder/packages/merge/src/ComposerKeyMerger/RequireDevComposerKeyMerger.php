<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger;

use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;

final class RequireDevComposerKeyMerger extends AbstractComposerKeyMerger implements ComposerKeyMergerInterface
{
    public function merge(ComposerJson $rootComposerJson, ComposerJson $jsonToMerge): void
    {
        if ($jsonToMerge->getRequireDev() === []) {
            return;
        }

        $requireDev = $this->mergeRecursiveAndSort($rootComposerJson->getRequireDev(), $jsonToMerge->getRequireDev());
        $rootComposerJson->setRequireDev($requireDev);
    }
}
