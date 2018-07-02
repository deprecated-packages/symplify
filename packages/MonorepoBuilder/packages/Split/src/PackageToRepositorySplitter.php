<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Symplify\MonorepoBuilder\Split\Exception\PackageToRepositorySplitException;
use Symplify\MonorepoBuilder\Split\Process\ProcessFactory;
use Symplify\MonorepoBuilder\Split\Process\SplitProcessInfo;
use Symplify\MonorepoBuilder\Split\Process\SplitProcessInfoFactory;
use Symplify\PackageBuilder\FileSystem\FileSystemGuard;

final class PackageToRepositorySplitter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var Process[]
     */
    private $activeProcesses = [];

    /**
     * @var SplitProcessInfo[]
     */
    private $processInfos = [];

    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    /**
     * @var ProcessFactory
     */
    private $processFactory;

    /**
     * @var SplitProcessInfoFactory
     */
    private $splitProcessInfoFactory;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        FileSystemGuard $fileSystemGuard,
        ProcessFactory $processFactory,
        SplitProcessInfoFactory $splitProcessInfoFactory
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->fileSystemGuard = $fileSystemGuard;
        $this->processFactory = $processFactory;
        $this->splitProcessInfoFactory = $splitProcessInfoFactory;
    }

    /**
     * @param mixed[] $splitConfig
     */
    public function splitDirectoriesToRepositories(
        array $splitConfig,
        string $rootDirectory,
        bool $isVerbose
    ): void {
        $theMostRecentTag = $this->getMostRecentTag($rootDirectory);

        foreach ($splitConfig as $localSubdirectory => $remoteRepository) {
            $this->fileSystemGuard->ensureDirectoryExists($localSubdirectory);

            $process = $this->processFactory->createSubsplit(
                $theMostRecentTag,
                $localSubdirectory,
                $remoteRepository,
                $isVerbose
            );

            $this->symfonyStyle->note('Running: ' . $process->getCommandLine());
            $process->start();

            $this->activeProcesses[] = $process;

            $this->processInfos[] = $this->splitProcessInfoFactory->createFromProcessLocalDirectoryAndRemoteRepository(
                $process,
                $localSubdirectory,
                $remoteRepository
            );
        }

        $this->symfonyStyle->success(sprintf('Running %d jobs asynchronously', count($this->activeProcesses)));

        while (count($this->activeProcesses)) {
            foreach ($this->activeProcesses as $i => $runningProcess) {
                if (! $runningProcess->isRunning()) {
                    unset($this->activeProcesses[$i]);
                } else {
                    $incrementalOutput = trim($runningProcess->getIncrementalOutput());
                    if ($incrementalOutput) {
                        $this->symfonyStyle->note($incrementalOutput);
                    }
                }
            }

            // check every second
            sleep(1);
        }

        $this->reportFinishedProcesses();
    }

    private function getMostRecentTag(string $cwd): string
    {
        $process = new Process('git tag -l --sort=committerdate', $cwd);
        $process->run();
        $tags = $process->getOutput();
        $tagList = explode(PHP_EOL, trim($tags));

        return (string) array_pop($tagList);
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

            $this->symfonyStyle->success(sprintf(
                'Push of "%s" directory to "%s" repository was successful: %s',
                $processInfo->getLocalDirectory(),
                $processInfo->getRemoteRepository(),
                $process->getOutput()
            ));
        }
    }
}
