<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Arrays\SortedParameterMerger;
use Symplify\MonorepoBuilder\Merge\Cleaner\RequireRequireDevDuplicateCleaner;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;

final class RequireComposerKeyMerger implements ComposerKeyMergerInterface
{
    /**
     * @var SortedParameterMerger
     */
    private $sortedParameterMerger;

    /**
     * @var RequireRequireDevDuplicateCleaner
     */
    private $requireRequireDevDuplicateCleaner;

    public function __construct(
        SortedParameterMerger $sortedParameterMerger,
        RequireRequireDevDuplicateCleaner $requireRequireDevDuplicateCleaner
    ) {
        $this->sortedParameterMerger = $sortedParameterMerger;
        $this->requireRequireDevDuplicateCleaner = $requireRequireDevDuplicateCleaner;
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
