<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\DependencyUpdater;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Package\PackageNamesProvider;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Utils\VersionUtils;

final class SetCurrentMutualDependenciesReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var VersionUtils
     */
    private $versionUtils;

    /**
     * @var DependencyUpdater
     */
    private $dependencyUpdater;

    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var PackageNamesProvider
     */
    private $packageNamesProvider;

    public function __construct(
        VersionUtils $versionUtils,
        DependencyUpdater $dependencyUpdater,
        ComposerJsonProvider $composerJsonProvider,
        PackageNamesProvider $packageNamesProvider
    ) {
        $this->versionUtils = $versionUtils;
        $this->dependencyUpdater = $dependencyUpdater;
        $this->composerJsonProvider = $composerJsonProvider;
        $this->packageNamesProvider = $packageNamesProvider;
    }

    public function work(Version $version): void
    {
        $versionInString = $this->versionUtils->getRequiredFormat($version);

        $this->dependencyUpdater->updateFileInfosWithPackagesAndVersion(
            $this->composerJsonProvider->getPackagesComposerFileInfos(),
            $this->packageNamesProvider->provide(),
            $versionInString
        );

        // give time to propagate values before commit
        sleep(1);
    }

    public function getDescription(Version $version): string
    {
        $versionInString = $this->versionUtils->getRequiredFormat($version);

        return sprintf('Set packages mutual dependencies to "%s" version', $versionInString);
    }
}
