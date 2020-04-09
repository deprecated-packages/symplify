<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;

final class RequireDevComposerKeyMerger extends AbstractComposerKeyMerger implements ComposerKeyMergerInterface
{
    public function merge(ComposerJson $mainComposerJson, ComposerJson $newComposerJson): void
    {
        if ($newComposerJson->getRequireDev() === []) {
            return;
        }

        $requireDev = $this->mergeAndSort($newComposerJson->getRequireDev(), $mainComposerJson->getRequireDev());
        $mainComposerJson->setRequireDev($requireDev);
    }
}
