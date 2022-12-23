<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger;

use PharIo\Version\InvalidPreReleaseSuffixException;
use PharIo\Version\PreReleaseSuffix;
use Symplify\MonorepoBuilder\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;

/**
 * @see \Symplify\MonorepoBuilder\Tests\Merge\ComposerKeyMerger\MinimalStabilityKeyMergerTest
 */
final class MinimalStabilityKeyMerger implements ComposerKeyMergerInterface
{
    public function merge(ComposerJson $mainComposerJson, ComposerJson $newComposerJson): void
    {
        try {
            $newStability = new PreReleaseSuffix((string) $newComposerJson->getMinimumStability());
        } catch (InvalidPreReleaseSuffixException) {
            return;
        }

        try {
            $mainStability = new PreReleaseSuffix((string) $mainComposerJson->getMinimumStability());
        } catch (InvalidPreReleaseSuffixException) {
            $mainStability = null;
        }

        if (!$mainStability instanceof PreReleaseSuffix || $mainStability->isGreaterThan($newStability)) {
            $mainComposerJson->setMinimumStability($newStability->asString());
        }
    }
}
