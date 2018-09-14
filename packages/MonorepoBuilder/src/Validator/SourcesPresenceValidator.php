<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Validator;

use Symplify\MonorepoBuilder\Exception\Validator\InvalidComposerJsonSetupException;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;

final class SourcesPresenceValidator
{
    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var string[]
     */
    private $packageDirectories = [];

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
        if (! count($composerPackageFiles)) {
            throw new InvalidComposerJsonSetupException(sprintf(
                'No package "composer.json" was found in package directories: "%s". Add "composer.json" or configure another directory in "parameters > package_directories"',
                implode('", "', $this->packageDirectories)
            ));
        }
    }

    public function validateRootComposerJsonName(): void
    {
        $rootComposerJson = $this->composerJsonProvider->getRootJson();
        if (! isset($rootComposerJson['name'])) {
            throw new InvalidComposerJsonSetupException('Complete "name" to your root "composer.json".');
        }
    }
}
