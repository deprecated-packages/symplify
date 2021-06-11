<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\ConflictingUpdater;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Package\PackageNamesProvider;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Utils\VersionUtils;

final class SetCurrentMutualConflictsReleaseWorker implements ReleaseWorkerInterface
{
    public function __construct(
        private VersionUtils $versionUtils,
        private ComposerJsonProvider $composerJsonProvider,
        private PackageNamesProvider $packageNamesProvider,
        private ConflictingUpdater $conflictingUpdater
    ) {
    }

    public function work(Version $version): void
    {
        $this->conflictingUpdater->updateFileInfosWithVendorAndVersion(
            $this->composerJsonProvider->getPackagesComposerFileInfos(),
            $this->packageNamesProvider->provide(),
            $version
        );

        // give time to propagate printed composer.json values before commit
        sleep(1);
    }

    public function getDescription(Version $version): string
    {
        $versionInString = $this->versionUtils->getRequiredFormat($version);

        return sprintf('Set packages mutual conflicts to "%s" version', $versionInString);
    }
}
