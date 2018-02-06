<?php declare(strict_types=1);

namespace Symplify\Monorepo;

use GitWrapper\GitWorkingCopy;
use GitWrapper\GitWrapper;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\Monorepo\Configuration\RepositoryGuard;
use Symplify\Monorepo\Filesystem\Filesystem;
use Symplify\Monorepo\Worker\MoveHistoryWorker;
use Symplify\Monorepo\Worker\RepositoryWorker;

final class RepositoryToPackageMerger
{
    /**
     * @var RepositoryWorker
     */
    private $repositoryWorker;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var GitWrapper
     */
    private $gitWrapper;

    /**
     * @var MoveHistoryWorker
     */
    private $moveHistoryWorker;

    /**
     * @var RepositoryGuard
     */
    private $repositoryGuard;

    public function __construct(
        GitWrapper $gitWrapper,
        RepositoryWorker $repositoryWorker,
        Filesystem $filesystem,
        SymfonyStyle $symfonyStyle,
        MoveHistoryWorker $moveHistoryWorker,
        RepositoryGuard $repositoryGuard
    ) {
        $this->gitWrapper = $gitWrapper;
        $this->repositoryWorker = $repositoryWorker;
        $this->filesystem = $filesystem;
        $this->symfonyStyle = $symfonyStyle;
        $this->moveHistoryWorker = $moveHistoryWorker;
        $this->repositoryGuard = $repositoryGuard;
    }

    public function mergeRepositoryToPackage(
        string $repositoryUrl,
        string $monorepoDirectory,
        string $packageSubdirectory
    ): void {
        $this->repositoryGuard->ensureIsRepository($repositoryUrl);
        $absolutePackageDirectory = $monorepoDirectory . '/' . $packageSubdirectory;
        $gitWorkingCopy = $this->getGitWorkingCopyForDirectory($monorepoDirectory);

        $this->symfonyStyle->title(sprintf('Build "%s" into "%s" directory', $repositoryUrl, $packageSubdirectory));

        // add repository as remote and merge
        $this->addAndMergeRemoteRepository($repositoryUrl, $monorepoDirectory, $gitWorkingCopy);

        $fileInfos = $this->filesystem->findMergedPackageFiles($monorepoDirectory);

        $this->copyFilesToPackageSubdirectory(
            $repositoryUrl,
            $packageSubdirectory,
            $absolutePackageDirectory,
            $fileInfos,
            $gitWorkingCopy
        );

        $this->moveHistoryToPackageSubdirectory($monorepoDirectory, $packageSubdirectory, $fileInfos);

        $this->clearOriginalRepositoryFiles($repositoryUrl, $monorepoDirectory, $packageSubdirectory, $gitWorkingCopy);

        $this->symfonyStyle->newLine(2);
    }

    private function getGitWorkingCopyForDirectory(string $directory): GitWorkingCopy
    {
        $gitWorkingCopy = $this->gitWrapper->workingCopy($directory);
        // be sure it's git repository
        $gitWorkingCopy->init();

        return $gitWorkingCopy;
    }

    private function clearOriginalRepositoryFiles(
        string $repositoryUrl,
        string $monorepoDirectory,
        string $packageSubdirectory,
        GitWorkingCopy $gitWorkingCopy
    ): void {
        $this->symfonyStyle->note(sprintf('Cleaning up files moved to "%s"', $packageSubdirectory));
        $this->filesystem->deleteMergedPackage($monorepoDirectory, $packageSubdirectory);
        if ($gitWorkingCopy->hasChanges()) {
            $gitWorkingCopy->add('.');
            $gitWorkingCopy->commit(sprintf('[Monorepo] Remove original files for "%s"', $repositoryUrl));
        }

        $this->symfonyStyle->success('Done');
    }

    /**
     * @param SplFileInfo[] $fileInfos
     */
    private function moveHistoryToPackageSubdirectory(
        string $monorepoDirectory,
        string $packageSubdirectory,
        array $fileInfos
    ): void {
        $this->symfonyStyle->note(sprintf(
            'Rewriting history for %d files to "%s" directory',
            count($fileInfos),
            $packageSubdirectory
        ));
        $this->moveHistoryWorker->prependHistoryToNewPackageFiles($fileInfos, $monorepoDirectory, $packageSubdirectory);
        $this->symfonyStyle->success('Done');
    }

    /**
     * @param SplFileInfo[] $fileInfos
     */
    private function copyFilesToPackageSubdirectory(
        string $repositoryUrl,
        string $packageSubdirectory,
        string $absolutePackageDirectory,
        array $fileInfos,
        GitWorkingCopy $gitWorkingCopy
    ): void {
        $this->symfonyStyle->note(sprintf(
            'Copying files for "%s" from root to "%s"',
            $repositoryUrl,
            $absolutePackageDirectory
        ));
        $this->filesystem->copyFinderFilesToDirectory($fileInfos, $absolutePackageDirectory);
        if ($gitWorkingCopy->hasChanges()) {
            $gitWorkingCopy->add('.');
            $gitWorkingCopy->commit(sprintf(
                '[Monorepo] Copy files from "%s" repository to "%s" directory',
                $repositoryUrl,
                $packageSubdirectory
            ));
        }

        $this->symfonyStyle->success('Done');
    }

    private function addAndMergeRemoteRepository(
        string $repositoryUrl,
        string $monorepoDirectory,
        GitWorkingCopy $gitWorkingCopy
    ): void {
        $this->symfonyStyle->note(sprintf(
            'Adding and merging "%s" remote repository to "%s" directory',
            $repositoryUrl,
            $monorepoDirectory
        ));
        $this->repositoryWorker->mergeRepositoryToMonorepoDirectory($repositoryUrl, $gitWorkingCopy);
        $this->symfonyStyle->success('Done');
    }
}
