<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Command;

use Nette\Utils\Strings;
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
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\StageAwareInterface;
use Symplify\MonorepoBuilder\Release\Exception\ConflictingPriorityException;
use Symplify\MonorepoBuilder\Release\Guard\ReleaseGuard;
use Symplify\MonorepoBuilder\Split\Git\GitManager;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use function Safe\getcwd;
use function Safe\krsort;
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
     * @var ReleaseGuard
     */
    private $releaseGuard;

    /**
     * @param ReleaseWorkerInterface[] $releaseWorkers
     */
    public function __construct(
        SymfonyStyle $symfonyStyle,
        GitManager $gitManager,
        array $releaseWorkers,
        bool $enableDefaultReleaseWorkers,
        ReleaseGuard $releaseGuard
    ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->gitManager = $gitManager;
        $this->releaseGuard = $releaseGuard;

        $this->setWorkersAndSortByPriority($releaseWorkers, $enableDefaultReleaseWorkers);
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
            'Do not perform operations, just their preview'
        );

        $this->addOption(Option::STAGE, null, InputOption::VALUE_REQUIRED, 'Name of stage to perform');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // validation phase
        $this->releaseGuard->guardStage($input->getOption(Option::STAGE));

        /** @var string $versionArgument */
        $versionArgument = $input->getArgument(Option::VERSION);

        $version = $this->createValidVersion($versionArgument);

        $isDryRun = (bool) $input->getOption(Option::DRY_RUN);

        $activeReleaseWorkers = $this->resolveActiveReleaseWorkers($input->getOption(Option::STAGE));

        $totalWorkerCount = count($activeReleaseWorkers);
        $i = 0;

        foreach ($activeReleaseWorkers as $releaseWorker) {
            $title = sprintf('%d/%d) %s', ++$i, $totalWorkerCount, $releaseWorker->getDescription($version));
            $this->symfonyStyle->title($title);

            $this->printReleaseWorkerMetadata($releaseWorker);

            if ($isDryRun === false) {
                $releaseWorker->work($version);
            }
        }

        if ($isDryRun) {
            $this->symfonyStyle->note('Running in dry mode, nothing is changed');
        } else {
            $this->symfonyStyle->success(sprintf('Version "%s" is now released!', $version->getVersionString()));
        }

        return ShellCode::SUCCESS;
    }

    /**
     * @param ReleaseWorkerInterface[] $releaseWorkers
     */
    private function setWorkersAndSortByPriority(array $releaseWorkers, bool $enableDefaultReleaseWorkers): void
    {
        foreach ($releaseWorkers as $releaseWorker) {
            if ($this->shouldSkip($releaseWorker, $enableDefaultReleaseWorkers)) {
                continue;
            }

            $priority = $releaseWorker->getPriority();
            if (isset($this->releaseWorkersByPriority[$priority])) {
                throw new ConflictingPriorityException($releaseWorker, $this->releaseWorkersByPriority[$priority]);
            }

            $this->releaseWorkersByPriority[$priority] = $releaseWorker;
        }

        krsort($this->releaseWorkersByPriority);
    }

    private function createValidVersion(string $versionArgument): Version
    {
        // this object performs validation of version
        $version = new Version($versionArgument);
        $this->ensureVersionIsNewerThanLastOne($version);

        return $version;
    }

    /**
     * @return ReleaseWorkerInterface[]
     */
    private function resolveActiveReleaseWorkers(?string $stage): array
    {
        if ($stage === null) {
            return $this->releaseWorkersByPriority;
        }

        $activeReleaseWorkers = [];
        foreach ($this->releaseWorkersByPriority as $releaseWorker) {
            if ($releaseWorker instanceof StageAwareInterface) {
                if ($stage === $releaseWorker->getStage()) {
                    $activeReleaseWorkers[] = $releaseWorker;
                }
            }
        }

        return $activeReleaseWorkers;
    }

    private function printReleaseWorkerMetadata(ReleaseWorkerInterface $releaseWorker): void
    {
        if ($this->symfonyStyle->isVerbose() === false) {
            return;
        }

        // show priority and class on -v/--verbose/--debug
        $this->symfonyStyle->writeln('priority: ' . $releaseWorker->getPriority());
        $this->symfonyStyle->writeln('class: ' . get_class($releaseWorker));
        $this->symfonyStyle->newLine();
    }

    private function shouldSkip(ReleaseWorkerInterface $releaseWorker, bool $enableDefaultReleaseWorkers): bool
    {
        if ($enableDefaultReleaseWorkers) {
            return false;
        }

        return Strings::startsWith(get_class($releaseWorker), 'Symplify\MonorepoBuilder\Release');
    }

    private function ensureVersionIsNewerThanLastOne(Version $version): void
    {
        $mostRecentVersion = new Version($this->gitManager->getMostRecentTag(getcwd()));
        if ($version->isGreaterThan($mostRecentVersion)) {
            return;
        }

        throw new InvalidGitVersionException(sprintf(
            'Provided version "%s" must be never than the last one: "%s"',
            $version->getVersionString(),
            $mostRecentVersion->getVersionString()
        ));
    }
}
