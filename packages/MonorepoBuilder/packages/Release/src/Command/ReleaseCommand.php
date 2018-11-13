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
use Symplify\MonorepoBuilder\Exception\Git\InvalidGitVersionException;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Exception\ConflictingPriorityException;
use Symplify\MonorepoBuilder\Split\Git\GitManager;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use function Safe\getcwd;
use function Safe\sprintf;

final class ReleaseCommand extends Command
{
    /**
     * @var ReleaseWorkerInterface[]
     */
    private $releaseWorkersByPriority = [];

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var GitManager
     */
    private $gitManager;

    /**
     * @param ReleaseWorkerInterface[] $releaseWorkers
     */
    public function __construct(SymfonyStyle $symfonyStyle, GitManager $gitManager, array $releaseWorkers)
    {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->gitManager = $gitManager;

        $this->setWorkersAndSortByPriority($releaseWorkers);
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Perform release process with set Release Workers.');

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
            $releaseWorker->work($version, $isDryRun);
        }

        $this->symfonyStyle->success(sprintf('Version "%s" is now released!', $version->getVersionString()));

        return ShellCode::SUCCESS;
    }

    /**
     * @param ReleaseWorkerInterface[] $releaseWorkers
     */
    private function setWorkersAndSortByPriority(array $releaseWorkers): void
    {
        foreach ($releaseWorkers as $releaseWorker) {
            $priority = $releaseWorker->getPriority();
            if (isset($this->releaseWorkersByPriority[$priority])) {
                throw new ConflictingPriorityException($releaseWorker, $this->releaseWorkersByPriority[$priority]);
            }

            $this->releaseWorkersByPriority[$priority] = $releaseWorker;
        }

        krsort($this->releaseWorkersByPriority);
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
