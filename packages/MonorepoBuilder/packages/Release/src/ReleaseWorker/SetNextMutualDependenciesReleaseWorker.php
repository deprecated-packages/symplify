<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\InterdependencyUpdater;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Utils\Utils;

final class SetNextMutualDependenciesReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var InterdependencyUpdater
     */
    private $interdependencyUpdater;

    /**
     * @var Utils
     */
    private $utils;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        ComposerJsonProvider $composerJsonProvider,
        InterdependencyUpdater $interdependencyUpdater,
        Utils $utils
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->composerJsonProvider = $composerJsonProvider;
        $this->interdependencyUpdater = $interdependencyUpdater;
        $this->utils = $utils;
    }

    public function getPriority(): int
    {
        return 200;
    }

    public function work(Version $version, bool $isDryRun): void
    {
        $versionInString = $this->utils->getRequiredNextFormat($version);

        $this->symfonyStyle->note(sprintf('Setting packages mutual dependencies to "%s" version', $versionInString));

        $rootComposerJson = $this->composerJsonProvider->getRootJson();

        // @todo resolve better for only found packages
        // see https://github.com/Symplify/Symplify/pull/1037/files
        [$vendor,] = explode('/', $rootComposerJson['name']);

        $this->interdependencyUpdater->updateFileInfosWithVendorAndVersion(
            $this->composerJsonProvider->getPackagesFileInfos(),
            $vendor,
            $versionInString
        );

        $this->symfonyStyle->success('Done!');
    }
}
