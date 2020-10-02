<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Symplify\MonorepoBuilder\Split\Exception\PackageToRepositorySplitException;
use Symplify\MonorepoBuilder\Split\Git\GitManager;
use Symplify\MonorepoBuilder\Split\Process\ProcessFactory;
use Symplify\MonorepoBuilder\Split\ValueObject\SplitProcessInfo;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\SmartFileSystem\Exception\DirectoryNotFoundException;
use Symplify\SmartFileSystem\FileSystemGuard;

final class PackageToRepositorySplitter
{
    /**
     * @var Process[]
     */
    private $activeProcesses = [];

    /**
     * @var SplitProcessInfo[]
     */
    private $splitProcessInfos = [];

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    /**
     * @var ProcessFactory
     */
    private $processFactory;

    /**
     * @var GitManager
     */
    private $gitManager;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        FileSystemGuard $fileSystemGuard,
        ProcessFactory $processFactory,
        GitManager $gitManager
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->fileSystemGuard = $fileSystemGuard;
        $this->processFactory = $processFactory;
        $this->gitManager = $gitManager;
    }

    /**
     * @param array<string, string> $splitConfig
     * @throws PackageToRepositorySplitException
     * @throws DirectoryNotFoundException
     */
    public function splitDirectoriesToRepositories(
        array $splitConfig,
        string $rootDirectory,
        ?string $branch = null,
        ?int $maxProcesses = null,
        ?string $tag = null
    ): void {
        if ($tag === null) {
            // If user let the tool to automatically select the last tag, check if there are valid
            if ($this->gitManager->doTagsHaveCommitterDate()) {
                $message = sprintf(
                    'Some of the tags on this repository do not have committer date. This may lead to unwanted tag selection during split. You may want to use the "%s" parameter.',
                    Option::TAG
                );
                $this->symfonyStyle->warning($message);
            }

            $tag = $this->gitManager->getMostRecentTag($rootDirectory);
        }

        // If branch not set, default to current branch
        $branch = $branch ?? $this->gitManager->getCurrentBranch();

        // If branch doesn't exist on origin, push it
        if (! $this->gitManager->doesBranchExistOnRemote($branch)) {
            $missingBranchMessage = sprintf('Branch "%s" does not exist on origin, pushing it...', $branch);
            $this->symfonyStyle->note($missingBranchMessage);
            $this->symfonyStyle->writeln($this->gitManager->pushBranchToRemoteOrigin($branch));
        }

        foreach ($splitConfig as $localDirectory => $remoteRepository) {
            $this->fileSystemGuard->ensureDirectoryExists($localDirectory);

            $remoteRepositoryWithGithubKey = $this->gitManager->completeRemoteRepositoryWithGithubToken(
                $remoteRepository
            );

            $process = $this->processFactory->createSubsplit(
                $tag,
                $localDirectory,
                $remoteRepositoryWithGithubKey,
                $branch
            );

            $this->symfonyStyle->note('Running: ' . $process->getCommandLine());
            $process->start();

            $this->activeProcesses[] = $process;
            $this->splitProcessInfos[] = new SplitProcessInfo($process, $localDirectory, $remoteRepository);

            if ($maxProcesses && count($this->activeProcesses) === $maxProcesses) {
                $this->processActiveProcesses(1);
            }
        }

        $activeProcessCount = count($this->activeProcesses);

        $missingBranchMessage = sprintf('Running %d jobs in parallel', $activeProcessCount);
        $this->symfonyStyle->success($missingBranchMessage);

        $this->processActiveProcesses($activeProcessCount);
        $this->reportFinishedProcesses();
    }

    private function processActiveProcesses(int $numberOfProcessesToWaitFor): void
    {
        while ($numberOfProcessesToWaitFor > 0) {
            foreach ($this->activeProcesses as $i => $runningProcess) {
                if (! $runningProcess->isRunning()) {
                    unset($this->activeProcesses[$i]);
                    --$numberOfProcessesToWaitFor;
                }
            }

            // check every second
            sleep(1);
        }
    }

    private function reportFinishedProcesses(): void
    {
        foreach ($this->splitProcessInfos as $processInfo) {
            $process = $processInfo->getProcess();

            if (! $process->isSuccessful()) {
                $message = sprintf(
                    'Process failed with "%d" code: "%s"',
                    $process->getExitCode(),
                    $process->getErrorOutput()
                );

                throw new PackageToRepositorySplitException($message);
            }

            $successMessage = sprintf(
                'Push of "%s" directory to "%s" repository was successful: %s "%s"',
                $processInfo->getLocalDirectory(),
                $processInfo->getRemoteRepository(),
                PHP_EOL . PHP_EOL,
                $process->getOutput()
            );

            $this->symfonyStyle->success($successMessage);
        }
    }
}
