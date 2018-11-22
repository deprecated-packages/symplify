<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;
use function Safe\sprintf;

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

    public function getDescription(Version $version): string
    {
        $versionInString = $this->utils->getRequiredNextFormat($version);

        return sprintf('Set packages mutual dependencies to "%s" (alias of dev version)', $versionInString);
    }
}
