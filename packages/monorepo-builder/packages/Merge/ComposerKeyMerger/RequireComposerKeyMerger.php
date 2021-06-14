<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Arrays\SortedParameterMerger;
use Symplify\MonorepoBuilder\Merge\Cleaner\RequireRequireDevDuplicateCleaner;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;

final class RequireComposerKeyMerger implements ComposerKeyMergerInterface
{
    public function __construct(
        private SortedParameterMerger $sortedParameterMerger,
        private RequireRequireDevDuplicateCleaner $requireRequireDevDuplicateCleaner
    ) {
    }

    public function merge(ComposerJson $mainComposerJson, ComposerJson $newComposerJson): void
    {
        if ($newComposerJson->getRequire() === []) {
            return;
        }

        $require = $this->sortedParameterMerger->mergeAndSort(
            $newComposerJson->getRequire(),
            $mainComposerJson->getRequire()
        );

        $mainComposerJson->setRequire($require);

        $requireDev = $this->requireRequireDevDuplicateCleaner->unsetPackageFromRequire(
            $mainComposerJson,
            $mainComposerJson->getRequireDev()
        );
        $mainComposerJson->setRequireDev($requireDev);
    }
}
