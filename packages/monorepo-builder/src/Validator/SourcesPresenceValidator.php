<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Validator;

use Symplify\MonorepoBuilder\Exception\Validator\InvalidComposerJsonSetupException;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;

final class SourcesPresenceValidator
{
    /**
     * @var string[]
     */
    private $packageDirectories = [];

    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @param string[] $packageDirectories
     */
    public function __construct(ComposerJsonProvider $composerJsonProvider, array $packageDirectories)
    {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->packageDirectories = $packageDirectories;
    }

    public function validatePackageComposerJsons(): void
    {
        $composerPackageFiles = $this->composerJsonProvider->getPackagesFileInfos();
        if (count($composerPackageFiles) > 0) {
            return;
        }

        throw new InvalidComposerJsonSetupException(sprintf(
            'No package "composer.json" was found in package directories: "%s". Add "composer.json" or configure another directory in "parameters > package_directories"',
            implode('", "', $this->packageDirectories)
        ));
    }

    public function validateRootComposerJsonName(): void
    {
        $mainComposerJson = $this->composerJsonProvider->getRootJson();
        if (isset($mainComposerJson['name'])) {
            return;
        }

        throw new InvalidComposerJsonSetupException('Complete "name" to your root "composer.json".');
    }
}
