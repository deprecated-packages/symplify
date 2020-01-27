<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger;

use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;

final class AutoloadComposerKeyMerger extends AbstractComposerKeyMerger implements ComposerKeyMergerInterface
{
    public function merge(ComposerJson $rootComposerJson, ComposerJson $jsonToMerge): void
    {
        if ($jsonToMerge->getAutoload() === []) {
            return;
        }

        $autoload = $this->mergeRecursiveAndSort($rootComposerJson->getAutoload(), $jsonToMerge->getAutoload());
        $rootComposerJson->setAutoload($autoload);
    }
}
