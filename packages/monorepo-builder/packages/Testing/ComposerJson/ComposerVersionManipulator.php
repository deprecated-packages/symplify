<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing\ComposerJson;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class ComposerVersionManipulator
{
    /**
     * @var string
     */
    private const COMPOSER_BRANCH_PREFIX = 'dev-';

    /**
     * @var string
     */
    private $branchAliasTarget;

    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->branchAliasTarget = self::COMPOSER_BRANCH_PREFIX . $parameterProvider->provideStringParameter(
            Option::DEFAULT_BRANCH_NAME
        );
    }

    /**
     * @param mixed[] $packageComposerJson
     * @param string[] $usedPackageNames
     * @return mixed[]
     */
    public function decorateAsteriskVersionForUsedPackages(array $packageComposerJson, array $usedPackageNames): array
    {
        foreach ([ComposerJsonSection::REQUIRE, ComposerJsonSection::REQUIRE_DEV] as $section) {
            foreach ($usedPackageNames as $usedPackageName) {
                if (! isset($packageComposerJson[$section][$usedPackageName])) {
                    continue;
                }

                $packageComposerJson[$section][$usedPackageName] = $this->branchAliasTarget;
            }
        }

        return $packageComposerJson;
    }
}
