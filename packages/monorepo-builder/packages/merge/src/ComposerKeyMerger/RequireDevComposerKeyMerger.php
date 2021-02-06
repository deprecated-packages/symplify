<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Arrays\SortedParameterMerger;
use Symplify\MonorepoBuilder\Merge\Cleaner\RequireRequireDevDuplicateCleaner;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;

final class RequireDevComposerKeyMerger implements ComposerKeyMergerInterface
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
        if ($newComposerJson->getRequireDev() === []) {
            return;
        }

        $requireDev = $this->sortedParameterMerger->mergeAndSort(
            $newComposerJson->getRequireDev(),
            $mainComposerJson->getRequireDev()
        );

        $requireDev = $this->requireRequireDevDuplicateCleaner->unsetPackageFromRequire($mainComposerJson, $requireDev);

        $mainComposerJson->setRequireDev($requireDev);
    }
}
