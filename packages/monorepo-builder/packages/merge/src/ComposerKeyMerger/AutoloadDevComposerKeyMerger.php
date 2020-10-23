<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;
use Symplify\MonorepoBuilder\Merge\Validation\AutoloadPathValidator;

final class AutoloadDevComposerKeyMerger extends AbstractComposerKeyMerger implements ComposerKeyMergerInterface
{
    /**
     * @var AutoloadPathValidator
     */
    private $autoloadPathValidator;

    public function __construct(AutoloadPathValidator $autoloadPathValidator)
    {
        $this->autoloadPathValidator = $autoloadPathValidator;
    }

    public function merge(ComposerJson $mainComposerJson, ComposerJson $newComposerJson): void
    {
        if ($newComposerJson->getAutoloadDev() === []) {
            return;
        }

        $this->autoloadPathValidator->ensureAutoloadPathExists($newComposerJson);

        $autoloadDev = $this->mergeRecursiveAndSort(
            $mainComposerJson->getAutoloadDev(),
            $newComposerJson->getAutoloadDev()
        );

        $mainComposerJson->setAutoloadDev($autoloadDev);
    }
}
