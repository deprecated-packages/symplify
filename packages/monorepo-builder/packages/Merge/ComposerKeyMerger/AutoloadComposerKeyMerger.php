<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Arrays\SortedParameterMerger;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;
use Symplify\MonorepoBuilder\Merge\Validation\AutoloadPathValidator;

final class AutoloadComposerKeyMerger implements ComposerKeyMergerInterface
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
        if ($newComposerJson->getAutoload() === []) {
            return;
        }

        $this->autoloadPathValidator->ensureAutoloadPathExists($newComposerJson);

        $autoload = $this->sortedParameterMerger->mergeRecursiveAndSort(
            $mainComposerJson->getAutoload(),
            $newComposerJson->getAutoload()
        );
        $mainComposerJson->setAutoload($autoload);
    }
}
