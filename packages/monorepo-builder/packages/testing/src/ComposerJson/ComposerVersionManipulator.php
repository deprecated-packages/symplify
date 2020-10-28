<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing\ComposerJson;

use OndraM\CiDetector\CiDetector;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;

final class ComposerVersionManipulator
{
    /**
     * @param mixed[] $packageComposerJson
     * @param string[] $usedPackageNames
     * @return mixed[]
     */
    public function setAsteriskVersionForUsedPackages(array $packageComposerJson, array $usedPackageNames): array
    {
        $ciDetector = new CiDetector();

        foreach ([ComposerJsonSection::REQUIRE, ComposerJsonSection::REQUIRE_DEV] as $section) {
            foreach ($usedPackageNames as $usedPackageName) {
                if (! isset($packageComposerJson[$section][$usedPackageName])) {
                    continue;
                }

                if ($ciDetector->isCiDetected()) {
                    $ci = $ciDetector->detect();
                    /** hotfix for Github Actions, @see https://github.com/composer/composer/issues/9368#issuecomment-718089230 */
                    $version = 'dev-' . $ci->getGitCommit() . ' || dev-master';
                } else {
                    $version = 'dev-master';
                }

                $packageComposerJson[$section][$usedPackageName] = $version;
            }
        }

        return $packageComposerJson;
    }
}
