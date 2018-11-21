<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;

final class SetCurrentMutualDependenciesReleaseWorker extends AbstractMutualDependencyReleaseWorker
{
    public function getPriority(): int
    {
        return 800;
    }

    public function work(Version $version): void
    {
        $versionInString = $this->utils->getRequiredFormat($version);

        $this->interdependencyUpdater->updateFileInfosWithPackagesAndVersion(
            $this->composerJsonProvider->getPackagesFileInfos(),
            $this->packageNamesProvider->provide(),
            $versionInString
        );
    }

    public function getDescription(): string
    {
        return 'Set packages mutual dependencies to release version';
    }
}
