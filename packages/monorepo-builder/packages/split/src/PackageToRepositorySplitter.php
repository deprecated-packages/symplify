<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symplify\MonorepoBuilder\Split\Closure\ClosureFactory;
use Symplify\MonorepoBuilder\Split\Configuration\Option;
use Symplify\MonorepoBuilder\Split\Exception\PackageToRepositorySplitException;
use Symplify\MonorepoBuilder\Split\Exception\UnsupportedGitVersionException;
use Symplify\MonorepoBuilder\Split\Git\GitManager;
use Symplify\SmartFileSystem\Exception\DirectoryNotFoundException;
use Symplify\SmartFileSystem\FileSystemGuard;
use Throwable;

final class PackageToRepositorySplitter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    /**
     * @var ClosureFactory
     */
    private $closureFactory;

    /**
     * @var GitManager
     */
    private $gitManager;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        FileSystemGuard $fileSystemGuard,
        ClosureFactory $closureFactory,
        GitManager $gitManager
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->fileSystemGuard = $fileSystemGuard;
        $this->closureFactory = $closureFactory;
        $this->gitManager = $gitManager;
    }

    /**
     * @param array<string, string> $splitConfig
     * @throws PackageToRepositorySplitException
     * @throws DirectoryNotFoundException
     * @throws UnsupportedGitVersionException
     * @throws ProcessFailedException
     */
    public function splitDirectoriesToRepositories(
        array $splitConfig,
        string $rootDirectory,
        ?string $branch = null,
        ?int $maxProcesses = null,
        ?string $tag = null,
        bool $dryRun = false
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

        $count = 0;
        foreach ($splitConfig as $localDirectory => $remoteRepository) {
            $this->fileSystemGuard->ensureDirectoryExists($localDirectory);

            $remoteRepositoryWithGithubKey = $this->gitManager->completeRemoteRepositoryWithGithubToken(
                $remoteRepository
            );

            $closure = $this->closureFactory->createSubsplit(
                $tag,
                $localDirectory,
                $remoteRepositoryWithGithubKey,
                $branch,
                $dryRun
            );

            try {
                call_user_func($closure);
            } catch (Throwable $throwable) {
                $status = sprintf('Failed to split %s to %s', $localDirectory, $remoteRepository);
                $this->symfonyStyle->error($status);
                throw $throwable;
            }

            $count++;
        }

        if (! $this->symfonyStyle->isQuiet()) {
            $status = sprintf('Succesfully split into %s repositories', $count);
            $this->symfonyStyle->success($status);
        }
    }
}
