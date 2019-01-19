<?php declare(strict_types=1);

namespace Symplify\Statie\MigratorJekyll\Filesystem;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\Statie\MigratorJekyll\Command\Reporter\MigrateJekyllReporter;

final class FilesystemMover
{
    /**
     * @var MigratorFilesystem
     */
    private $migratorFilesystem;

    /**
     * @var MigrateJekyllReporter
     */
    private $migrateJekyllReporter;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(
        MigratorFilesystem $migratorFilesystem,
        MigrateJekyllReporter $migrateJekyllReporter,
        SymfonyStyle $symfonyStyle
    ) {
        $this->migratorFilesystem = $migratorFilesystem;
        $this->migrateJekyllReporter = $migrateJekyllReporter;
        $this->symfonyStyle = $symfonyStyle;
    }

    /**
     * @param mixed[] $paths
     */
    public function processPaths(string $workingDirectory, array $paths): void
    {
        foreach ($paths as $oldPath => $newPath) {
            if (Strings::contains($oldPath, '*')) { // is match
                $this->processMatchPath($oldPath, $newPath, $workingDirectory);
            } else {
                $this->processMove($oldPath, $newPath, $workingDirectory);
            }
        }

        $this->symfonyStyle->success('Files moved');
    }

    private function processMatchPath(string $oldPath, string $newPath, string $workingDirectory): void
    {
        $foundFileInfos = $this->migratorFilesystem->findFilesWithGlob($oldPath, $workingDirectory);

        foreach ($foundFileInfos as $foundFileInfo) {
            $oldPath = $foundFileInfo->getRealPath();

            // new path is only directory
            $currentPath = rtrim($newPath, '/') . '/' . $foundFileInfo->getRelativeFilePathFromDirectory(
                $workingDirectory
            );

            $currentPath = $this->migratorFilesystem->absolutizePath($currentPath, $workingDirectory);

            $this->migrateJekyllReporter->reportPathOperation('Moved', $oldPath, $currentPath);

            FileSystem::rename($oldPath, $currentPath);
        }
    }

    private function processMove(string $oldPath, string $newPath, string $workingDirectory): void
    {
        $oldPath = $this->migratorFilesystem->absolutizePath($oldPath, $workingDirectory);
        if (! file_exists($oldPath)) {
            return;
        }

        $newPath = $this->migratorFilesystem->absolutizePath($newPath, $workingDirectory);
        $this->migrateJekyllReporter->reportPathOperation('Moved', $oldPath, $newPath);
        FileSystem::rename($oldPath, $newPath);
    }
}
