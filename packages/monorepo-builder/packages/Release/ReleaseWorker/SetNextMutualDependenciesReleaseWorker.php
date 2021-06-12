<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\DependencyUpdater;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Package\PackageNamesProvider;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Utils\VersionUtils;

final class SetNextMutualDependenciesReleaseWorker implements ReleaseWorkerInterface
{
    public function __construct(
        private ComposerJsonProvider $composerJsonProvider,
        private DependencyUpdater $dependencyUpdater,
        private PackageNamesProvider $packageNamesProvider,
        private VersionUtils $versionUtils
    ) {
    }

    public function work(Version $version): void
    {
        $versionInString = $this->versionUtils->getRequiredNextFormat($version);

        $this->dependencyUpdater->updateFileInfosWithPackagesAndVersion(
            $this->composerJsonProvider->getPackagesComposerFileInfos(),
            $this->packageNamesProvider->provide(),
            $versionInString
        );
    }

    public function getDescription(Version $version): string
    {
        $versionInString = $this->versionUtils->getRequiredNextFormat($version);

        return sprintf('Set packages mutual dependencies to "%s" (alias of dev version)', $versionInString);
    }
}
