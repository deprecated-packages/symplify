<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Command;

use PharIo\Version\Version;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Configuration\Option;
use Symplify\MonorepoBuilder\DevMasterAliasUpdater;
use Symplify\MonorepoBuilder\Exception\Git\InvalidGitVersionException;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\InterdependencyUpdater;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Split\Git\GitManager;
use Symplify\MonorepoBuilder\Utils\Utils;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use function Safe\getcwd;
use function Safe\sprintf;

final class ReleaseCommand extends Command
{
    /**
     * @var array
     */
    private $releaseWorkersByPriority = [];

    /**
     * @var array|ReleaseWorkerInterface[]
     */
    private $releaseWorkers = [];

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var GitManager
     */
    private $gitManager;

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

    /**
     * @var DevMasterAliasUpdater
     */
    private $devMasterAliasUpdater;

    /**
     * @param ReleaseWorkerInterface[] $releaseWorkers
     */
    public function __construct(
        SymfonyStyle $symfonyStyle,
        GitManager $gitManager,
        ComposerJsonProvider $composerJsonProvider,
        InterdependencyUpdater $interdependencyUpdater,
        Utils $utils,
        DevMasterAliasUpdater $devMasterAliasUpdater,
        array $releaseWorkers
    ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->gitManager = $gitManager;
        $this->composerJsonProvider = $composerJsonProvider;
        $this->interdependencyUpdater = $interdependencyUpdater;
        $this->utils = $utils;
        $this->devMasterAliasUpdater = $devMasterAliasUpdater;

        foreach ($releaseWorkers as $releaseWorker) {
            $this->releaseWorkersByPriority[$releaseWorker->getPriority()] = $releaseWorker;
        }
        krsort($this->releaseWorkersByPriority);

        dump($this->releaseWorkersByPriority);
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription(
            'Release new version, with tag, bump mutual dependency to pass, push with tag, then bump alias and mutual dependency to next version alias.'
        );
        $this->addArgument(
            Option::VERSION,
            InputArgument::REQUIRED,
            'Release version, in format "<major>.<minor>.<patch>" or "v<major>.<minor>.<patch>"'
        );

        $this->addOption(
            Option::DRY_RUN,
            null,
            InputOption::VALUE_NONE,
            'Do not perform git tagging operations, just their preview'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $versionArgument */
        $versionArgument = $input->getArgument(Option::VERSION);

        // this object performs validation of version
        $version = new Version($versionArgument);
        $this->ensureVersionIsNewerThanLastOne($version);

        $isDryRun = (bool) $input->getOption(Option::DRY_RUN);

        foreach ($this->releaseWorkersByPriority as $releaseWorker) {
            dump(get_class($releaseWorker));
//            $releaseWorker->work($version, $isDryRun);
        }

        $this->symfonyStyle->success(sprintf('Version "%s" is now released!', $version->getVersionString()));

        return ShellCode::SUCCESS;
    }

    private function ensureVersionIsNewerThanLastOne(Version $version): void
    {
        $mostRecentVersion = new Version($this->gitManager->getMostRecentTag(getcwd()));
        if ($version->isGreaterThan($mostRecentVersion)) {
            return;
        }

        throw new InvalidGitVersionException(sprintf(
            'Version "%s" is older than the last one "%s"',
            $version->getVersionString(),
            $mostRecentVersion->getVersionString()
        ));
    }
}
