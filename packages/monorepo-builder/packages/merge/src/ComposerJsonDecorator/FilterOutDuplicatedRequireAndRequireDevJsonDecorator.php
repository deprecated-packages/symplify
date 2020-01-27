<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerJsonDecorator;

use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerJsonDecoratorInterface;

final class FilterOutDuplicatedRequireAndRequireDevJsonDecorator implements ComposerJsonDecoratorInterface
{
    public function decorate(ComposerJson $composerJson): void
    {
        if ($composerJson->getRequire() === [] || $composerJson->getRequireDev() === []) {
            return;
        }

        $duplicatedPackages = array_intersect(
            array_keys($composerJson->getRequire()),
            array_keys($composerJson->getRequireDev())
        );

        $currentRequireDev = $composerJson->getRequireDev();

        foreach (array_keys($currentRequireDev) as $package) {
            if (in_array($package, $duplicatedPackages, true)) {
                unset($currentRequireDev[$package]);
            }
        }

        $composerJson->setRequireDev($currentRequireDev);
    }
}
