<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\DevMasterAliasUpdater;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

final class UpdateBranchAliasReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var DevMasterAliasUpdater
     */
    private $devMasterAliasUpdater;

    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    public function __construct(
        DevMasterAliasUpdater $devMasterAliasUpdater,
        ComposerJsonProvider $composerJsonProvider
    ) {
        $this->devMasterAliasUpdater = $devMasterAliasUpdater;
        $this->composerJsonProvider = $composerJsonProvider;
    }

    public function getPriority(): int
    {
        return 100;
    }

    public function work(Version $version): void
    {
        $this->devMasterAliasUpdater->updateFileInfosWithAlias(
            $this->composerJsonProvider->getPackagesFileInfos(),
            $version->getVersionString()
        );
    }

    public function getDescription(): string
    {
        return 'Set next dev version as branch alias to packages';
    }
}
