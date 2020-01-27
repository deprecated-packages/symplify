<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger;

use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;

final class RequireComposerKeyMerger extends AbstractComposerKeyMerger implements ComposerKeyMergerInterface
{
    public function merge(ComposerJson $rootComposerJson, ComposerJson $jsonToMerge): void
    {
        if ($jsonToMerge->getRequire() === []) {
            return;
        }

        $require = $this->mergeRecursiveAndSort($rootComposerJson->getRequire(), $jsonToMerge->getRequire());
        $rootComposerJson->setRequire($require);
    }
}
