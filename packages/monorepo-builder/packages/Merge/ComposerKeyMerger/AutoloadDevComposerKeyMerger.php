<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Arrays\SortedParameterMerger;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;
use Symplify\MonorepoBuilder\Merge\Validation\AutoloadPathValidator;

final class AutoloadDevComposerKeyMerger implements ComposerKeyMergerInterface
{
    /**
     * @var AutoloadPathValidator
     */
    private $autoloadPathValidator;

    /**
     * @var SortedParameterMerger
     */
    private $sortedParameterMerger;

    public function __construct(
        AutoloadPathValidator $autoloadPathValidator,
        SortedParameterMerger $sortedParameterMerger
    ) {
        $this->autoloadPathValidator = $autoloadPathValidator;
        $this->sortedParameterMerger = $sortedParameterMerger;
    }

    public function merge(ComposerJson $mainComposerJson, ComposerJson $newComposerJson): void
    {
        if ($newComposerJson->getAutoloadDev() === []) {
            return;
        }

        $this->autoloadPathValidator->ensureAutoloadPathExists($newComposerJson);

        $autoloadDev = $this->sortedParameterMerger->mergeRecursiveAndSort(
            $mainComposerJson->getAutoloadDev(),
            $newComposerJson->getAutoloadDev()
        );

        $mainComposerJson->setAutoloadDev($autoloadDev);
    }
}
