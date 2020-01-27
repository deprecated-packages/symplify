<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger;

use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;

final class ExtraComposerKeyMerger extends AbstractComposerKeyMerger implements ComposerKeyMergerInterface
{
    public function merge(ComposerJson $rootComposerJson, ComposerJson $jsonToMerge): void
    {
        if ($jsonToMerge->getExtra() === []) {
            return;
        }

        $extra = $this->parametersMerger->mergeWithCombine($rootComposerJson->getExtra(), $jsonToMerge->getExtra());

        // do not merge extra alias as only for local packages
        if (isset($extra['branch-alias'])) {
            unset($extra['branch-alias']);
        }

        if (! is_array($extra)) {
            return;
        }

        $rootComposerJson->setExtra($extra);
    }
}
