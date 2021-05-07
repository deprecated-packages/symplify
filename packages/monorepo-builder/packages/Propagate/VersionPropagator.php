<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Propagate;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;

final class VersionPropagator
{
    public function propagate(ComposerJson $mainComposerJson, ComposerJson $otherComposerJson): void
    {
        $packagesToVersions = array_merge($mainComposerJson->getRequire(), $mainComposerJson->getRequireDev());

        foreach ($packagesToVersions as $packageName => $packageVersion) {
            if (! $otherComposerJson->hasPackage($packageName)) {
                continue;
            }

            $otherComposerJson->changePackageVersion($packageName, $packageVersion);
        }
    }
}
