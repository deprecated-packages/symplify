<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Guard;

use Symplify\MonorepoBuilder\Exception\MissingComposerJsonFilesException;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;

final class ComposerJsonFilesGuard
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

    public function ensurePackageJsonFilesAreFound(): void
    {
        $composerPackageFiles = $this->composerJsonProvider->getPackagesFileInfos();
        if (count($composerPackageFiles)) {
            return;
        }

        throw new MissingComposerJsonFilesException(sprintf(
            'No package "composer.json" was found in "%s" directories. Add "composer.json" or configure another directory in "parameters > package_directories"',
            $this->packageDirectories
        ));
    }
}
