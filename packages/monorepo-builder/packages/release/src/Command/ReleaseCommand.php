<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Guard\ReleaseGuard;
use Symplify\MonorepoBuilder\Release\ReleaseWorkerProvider;
use Symplify\MonorepoBuilder\Release\ValueObject\SemVersion;
use Symplify\MonorepoBuilder\Release\Version\VersionFactory;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class ReleaseCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ReleaseGuard
     */
    private $releaseGuard;

    /**
     * @var ReleaseWorkerProvider
     */
    private $releaseWorkerProvider;

    /**
     * @var VersionFactory
     */
    private $versionFactory;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        ReleaseWorkerProvider $releaseWorkerProvider,
        ReleaseGuard $releaseGuard,
        VersionFactory $versionFactory
    ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->releaseGuard = $releaseGuard;
        $this->releaseWorkerProvider = $releaseWorkerProvider;
        $this->versionFactory = $versionFactory;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Perform release process with set Release Workers.');

        $description = sprintf(
            'Release version, in format "<major>.<minor>.<patch>" or "v<major>.<minor>.<patch> or one of keywords: "%s"',
            implode('", "', SemVersion::getAll())
        );
        $this->addArgument(Option::VERSION, InputArgument::REQUIRED, $description);

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
        $stage = $this->resolveStage($input);

        $this->releaseGuard->guardStage($stage);

        /** @var string $versionArgument */
        $versionArgument = $input->getArgument(Option::VERSION);
        $version = $this->versionFactory->createValidVersion($versionArgument, $stage);

        $activeReleaseWorkers = $this->releaseWorkerProvider->provideByStage($stage);

        $totalWorkerCount = count($activeReleaseWorkers);
        $i = 0;
        $isDryRun = (bool) $input->getOption(Option::DRY_RUN);

        foreach ($activeReleaseWorkers as $releaseWorker) {
            $title = sprintf('%d/%d) %s', ++$i, $totalWorkerCount, $releaseWorker->getDescription($version));
            $this->symfonyStyle->title($title);
            $this->printReleaseWorkerMetadata($releaseWorker);

            if (! $isDryRun) {
                $releaseWorker->work($version);
            }
        }

        if ($isDryRun) {
            $this->symfonyStyle->note('Running in dry mode, nothing is changed');
        } elseif ($stage === null) {
            $this->symfonyStyle->success(sprintf('Version "%s" is now released!', $version->getVersionString()));
        } else {
            $this->symfonyStyle->success(
                sprintf('Stage "%s" for version "%s" is now finished!', $stage, $version->getVersionString())
            );
        }

        return ShellCode::SUCCESS;
    }

    private function resolveStage(InputInterface $input): ?string
    {
        $stage = $input->getOption(Option::STAGE);

        return $stage !== null ? (string) $stage : $stage;
    }

    private function printReleaseWorkerMetadata(ReleaseWorkerInterface $releaseWorker): void
    {
        if (! $this->symfonyStyle->isVerbose()) {
            return;
        }

        // show priority and class on -v/--verbose/--debug
        $this->symfonyStyle->writeln('priority: ' . $releaseWorker->getPriority());
        $this->symfonyStyle->writeln('class: ' . get_class($releaseWorker));
        $this->symfonyStyle->newLine();
    }
}
