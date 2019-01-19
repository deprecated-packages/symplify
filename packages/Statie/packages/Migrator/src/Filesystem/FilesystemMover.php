<?php declare(strict_types=1);

namespace Symplify\Statie\Migrator\Filesystem;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\Statie\Migrator\Command\Reporter\MigrateReporter;

final class FilesystemMover
{
    /**
     * @var MigratorFilesystem
     */
    private $migratorFilesystem;

    /**
     * @var MigrateReporter
     */
    private $migrateReporter;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(
        MigratorFilesystem $migratorFilesystem,
        MigrateReporter $migrateReporter,
        SymfonyStyle $symfonyStyle
    ) {
        $this->migratorFilesystem = $migratorFilesystem;
        $this->migrateReporter = $migrateReporter;
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

            $this->migrateReporter->reportPathOperation('Moved', $oldPath, $currentPath);

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
        $this->migrateReporter->reportPathOperation('Moved', $oldPath, $newPath);
        FileSystem::rename($oldPath, $newPath);
    }
}
