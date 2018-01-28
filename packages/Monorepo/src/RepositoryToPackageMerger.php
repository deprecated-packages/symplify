<?php declare(strict_types=1);

namespace Symplify\Monorepo;

use GitWrapper\GitWorkingCopy;
use GitWrapper\GitWrapper;
use Symfony\Component\Console\Style\SymfonyStyle;
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

    public function __construct(GitWrapper $gitWrapper, RepositoryWorker $repositoryWorker, Filesystem $filesystem, SymfonyStyle $symfonyStyle, MoveHistoryWorker $moveHistoryWorker)
    {
        $this->gitWrapper = $gitWrapper;
        $this->repositoryWorker = $repositoryWorker;
        $this->filesystem = $filesystem;
        $this->symfonyStyle = $symfonyStyle;
        $this->moveHistoryWorker = $moveHistoryWorker;
    }

    public function mergeRepositoryToPackage(
        string $repositoryUrl,
        string $monorepoDirectory,
        string $packageSubdirectory
    ): void {
        $gitWorkingCopy = $this->getGitWorkingCopyForDirectory($monorepoDirectory);

        // add repository as remote and merge
        $this->repositoryWorker->mergeRepositoryToMonorepoDirectory($repositoryUrl, $gitWorkingCopy);
        $this->symfonyStyle->success(sprintf(
            'Repository "%s" was added as remote and merged in "%s"',
            $repositoryUrl,
            $monorepoDirectory
        ));

        // copy files into package subdirectory
        $absolutePackageDirectory = $monorepoDirectory . '/' . $packageSubdirectory;
        $finder = $this->filesystem->findMergedPackageFiles($monorepoDirectory);

        $this->filesystem->copyFinderFilesToDirectory($finder, $absolutePackageDirectory);

        //        if ($gitWorkingCopy->hasChanges()) {
//            $gitWorkingCopy->add('.');
//            $gitWorkingCopy->commit(sprintf('merge "%s" repository', $repositoryUrl));
//        }

        $this->symfonyStyle->success(sprintf(
            'Files for "%s" copied to "%s"',
            $repositoryUrl,
            $absolutePackageDirectory
        ));

        // prepend history
        $this->moveHistoryWorker->prependHistoryToNewPackageFiles($finder, $monorepoDirectory, $packageSubdirectory);
        $this->symfonyStyle->success(sprintf(
            'History added for "%s"',
            $packageSubdirectory
        ));
//        $this->filesystem->clearEmptyDirectories($monorepoDirectory);
    }

    private function getGitWorkingCopyForDirectory(string $directory): GitWorkingCopy
    {
        $gitWorkingCopy = $this->gitWrapper->workingCopy($directory);
        // be sure it's git repository
        $gitWorkingCopy->init();

        return $gitWorkingCopy;
    }
}
