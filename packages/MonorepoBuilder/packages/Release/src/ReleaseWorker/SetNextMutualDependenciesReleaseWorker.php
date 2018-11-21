<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;

final class SetNextMutualDependenciesReleaseWorker extends AbstractMutualDependencyReleaseWorker
{
    public function getPriority(): int
    {
        return 200;
    }

    public function work(Version $version): void
    {
        $versionInString = $this->utils->getRequiredNextFormat($version);

        $this->interdependencyUpdater->updateFileInfosWithPackagesAndVersion(
            $this->composerJsonProvider->getPackagesFileInfos(),
            $this->packageNamesProvider->provide(),
            $versionInString
        );
    }

    public function getDescription(): string
    {
        return 'Set packages mutual dependencies to alias of dev version';
    }
}
