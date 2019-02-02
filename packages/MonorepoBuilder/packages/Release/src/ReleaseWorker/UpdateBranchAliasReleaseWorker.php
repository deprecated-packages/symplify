<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\DevMasterAliasUpdater;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Utils\Utils;

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

    /**
     * @var Utils
     */
    private $utils;

    public function __construct(
        DevMasterAliasUpdater $devMasterAliasUpdater,
        ComposerJsonProvider $composerJsonProvider,
        Utils $utils
    ) {
        $this->devMasterAliasUpdater = $devMasterAliasUpdater;
        $this->composerJsonProvider = $composerJsonProvider;
        $this->utils = $utils;
    }

    public function getPriority(): int
    {
        return 100;
    }

    public function work(Version $version): void
    {
        $nextAlias = $this->utils->getNextAliasFormat($version);

        $this->devMasterAliasUpdater->updateFileInfosWithAlias(
            $this->composerJsonProvider->getPackagesFileInfos(),
            $nextAlias
        );
    }

    public function getDescription(Version $version): string
    {
        $nextAlias = $this->utils->getNextAliasFormat($version);

        return sprintf('Set branch alias "%s" to all packages', $nextAlias);
    }
}
