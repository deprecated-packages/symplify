<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\DevMasterAliasUpdater;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

final class UpdateBranchAliasReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var DevMasterAliasUpdater
     */
    private $devMasterAliasUpdater;

    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        DevMasterAliasUpdater $devMasterAliasUpdater,
        ComposerJsonProvider $composerJsonProvider
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->devMasterAliasUpdater = $devMasterAliasUpdater;
        $this->composerJsonProvider = $composerJsonProvider;
    }

    public function getPriority(): int
    {
        return 100;
    }

    public function work(Version $version, bool $isDryRun): void
    {
        $this->symfonyStyle->note(sprintf('Setting "%s" as branch dev alias to packages', $version));

        $this->devMasterAliasUpdater->updateFileInfosWithAlias(
            $this->composerJsonProvider->getPackagesFileInfos(),
            $version->getVersionString()
        );

        $this->symfonyStyle->success('Done!');
    }
}
