<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;

final class ExtraComposerKeyMerger extends AbstractComposerKeyMerger implements ComposerKeyMergerInterface
{
    public function merge(ComposerJson $mainComposerJson, ComposerJson $newComposerJson): void
    {
        if ($newComposerJson->getExtra() === []) {
            return;
        }

        $extra = $this->parametersMerger->mergeWithCombine($mainComposerJson->getExtra(), $newComposerJson->getExtra());

        // do not merge extra alias as only for local packages
        if (isset($extra['branch-alias'])) {
            unset($extra['branch-alias']);
        }

        if (! is_array($extra)) {
            return;
        }

        $mainComposerJson->setExtra($extra);
    }
}
