<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Symplify\MonorepoBuilder\Split\Exception\PackageToRepositorySplitException;
use Symplify\MonorepoBuilder\Split\Git\GitManager;
use Symplify\MonorepoBuilder\Split\Process\ProcessFactory;
use Symplify\MonorepoBuilder\Split\Process\SplitProcessInfo;
use Symplify\PackageBuilder\FileSystem\FileSystemGuard;

final class PackageToRepositorySplitter
{
    /**
     * @var Process[]
     */
    private $activeProcesses = [];

    /**
     * @var SplitProcessInfo[]
     */
    private $processInfos = [];

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
        GitManager $gitAnalyzer
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->fileSystemGuard = $fileSystemGuard;
        $this->processFactory = $processFactory;
        $this->gitManager = $gitAnalyzer;
    }

    /**
     * @param mixed[] $splitConfig
     */
    public function splitDirectoriesToRepositories(
        array $splitConfig,
        string $rootDirectory,
        ?int $maxProcesses = null
    ): void {
        $theMostRecentTag = $this->gitManager->getMostRecentTag($rootDirectory);
        foreach ($splitConfig as $localDirectory => $remoteRepository) {
            $this->fileSystemGuard->ensureDirectoryExists($localDirectory);

            $remoteRepositoryWithGithubKey = $this->gitManager->completeRemoteRepositoryWithGithubToken(
                $remoteRepository
            );

            $process = $this->processFactory->createSubsplit(
                $theMostRecentTag,
                $localDirectory,
                $remoteRepositoryWithGithubKey
            );

            $this->symfonyStyle->note('Running: ' . $process->getCommandLine());
            $process->start();

            $this->activeProcesses[] = $process;
            $this->processInfos[] = new SplitProcessInfo($process, $localDirectory, $remoteRepository);

            if ($maxProcesses && count($this->activeProcesses) === $maxProcesses) {
                $this->processActiveProcesses(1);
            }
        }

        $this->symfonyStyle->success(sprintf('Running %d jobs in parallel', count($this->activeProcesses)));

        $this->processActiveProcesses(count($this->activeProcesses));
        $this->reportFinishedProcesses();
    }

    private function processActiveProcesses(int $numberOfProcessesToWaitFor): void
    {
        while ($numberOfProcessesToWaitFor > 0) {
            foreach ($this->activeProcesses as $i => $runningProcess) {
                if (! $runningProcess->isRunning()) {
                    unset($this->activeProcesses[$i]);
                    $numberOfProcessesToWaitFor--;
                }
            }

            // check every second
            sleep(1);
        }
    }

    private function reportFinishedProcesses(): void
    {
        foreach ($this->processInfos as $processInfo) {
            $process = $processInfo->getProcess();

            if (! $process->isSuccessful()) {
                $message = sprintf(
                    'Process failed with "%d" code: "%s"',
                    $process->getExitCode(),
                    $process->getErrorOutput()
                );

                throw new PackageToRepositorySplitException($message);
            }

            $this->symfonyStyle->success(
                sprintf(
                    'Push of "%s" directory to "%s" repository was successful: %s "%s"',
                    $processInfo->getLocalDirectory(),
                    $processInfo->getRemoteRepository(),
                    PHP_EOL . PHP_EOL,
                    $process->getOutput()
                )
            );
        }
    }
}
