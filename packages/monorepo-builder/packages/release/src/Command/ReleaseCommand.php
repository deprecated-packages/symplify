<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Command;

use PharIo\Version\Version;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\StageAwareInterface;
use Symplify\MonorepoBuilder\Release\Guard\ReleaseGuard;
use Symplify\MonorepoBuilder\Release\ReleaseWorkerProvider;
use Symplify\MonorepoBuilder\Release\ValueObject\SemVersion;
use Symplify\MonorepoBuilder\Release\Version\VersionFactory;
use Symplify\MonorepoBuilder\Validator\SourcesPresenceValidator;
use Symplify\MonorepoBuilder\ValueObject\File;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;

final class ReleaseCommand extends AbstractSymplifyCommand
{
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

    /**
     * @var SourcesPresenceValidator
     */
    private $sourcesPresenceValidator;

    public function __construct(
        ReleaseWorkerProvider $releaseWorkerProvider,
        ReleaseGuard $releaseGuard,
        VersionFactory $versionFactory,
        SourcesPresenceValidator $sourcesPresenceValidator
    ) {
        parent::__construct();

        $this->releaseGuard = $releaseGuard;
        $this->releaseWorkerProvider = $releaseWorkerProvider;
        $this->versionFactory = $versionFactory;
        $this->sourcesPresenceValidator = $sourcesPresenceValidator;
    }

    protected function configure(): void
    {
        $this->setDescription('Perform release process with set Release Workers.');

        $description = sprintf(
            'Release version, in format "<major>.<minor>.<patch>" or "v<major>.<minor>.<patch> or one of keywords: "%s"',
            implode('", "', SemVersion::ALL)
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
        $this->sourcesPresenceValidator->validateRootComposerJsonName();

        // validation phase
        $stage = $this->resolveStage($input);

        $activeReleaseWorkers = $this->getReleaseWorkers($stage);
        if ($activeReleaseWorkers === []) {
            $errorMessage = sprintf(
                'There are no release workers registered. Be sure to add them to "%s"',
                File::CONFIG
            );
            $this->symfonyStyle->error($errorMessage);

            return ShellCode::ERROR;
        }

        $totalWorkerCount = count($activeReleaseWorkers);
        $i = 0;
        $isDryRun = (bool) $input->getOption(Option::DRY_RUN);

        $version = $this->resolveVersion($input, $stage);

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
            $message = sprintf('Version "%s" is now released!', $version->getVersionString());
            $this->symfonyStyle->success($message);
        } else {
            $finishedMessage = sprintf(
                'Stage "%s" for version "%s" is now finished!',
                $stage,
                $version->getVersionString()
            );
            $this->symfonyStyle->success($finishedMessage);
        }

        return ShellCode::SUCCESS;
    }

    private function printReleaseWorkerMetadata(ReleaseWorkerInterface $releaseWorker): void
    {
        if (! $this->symfonyStyle->isVerbose()) {
            return;
        }

        // show debug data on -v/--verbose/--debug
        $this->symfonyStyle->writeln('class: ' . get_class($releaseWorker));
        if ($releaseWorker instanceof StageAwareInterface) {
            $this->symfonyStyle->writeln('stage: ' . $releaseWorker->getStage());
        }

        $this->symfonyStyle->newLine();
    }

    private function resolveStage(InputInterface $input): ?string
    {
        $stage = $input->getOption(Option::STAGE);

        // string or null
        if ($stage === null) {
            $this->releaseGuard->guardRequiredStageOnEmptyStage();
            return null;
        }

        $stage = (string) $stage;
        $this->releaseGuard->guardStage($stage);

        return $stage;
    }

    private function resolveVersion(InputInterface $input, ?string $stage): Version
    {
        /** @var string $versionArgument */
        $versionArgument = $input->getArgument(Option::VERSION);

        return $this->versionFactory->createValidVersion($versionArgument, $stage);
    }

    /**
     * @return ReleaseWorkerInterface[]
     */
    private function getReleaseWorkers(?string $stage): array
    {
        if ($stage === null) {
            return $this->releaseWorkerProvider->provide();
        }

        return $this->releaseWorkerProvider->provideByStage($stage);
    }
}
