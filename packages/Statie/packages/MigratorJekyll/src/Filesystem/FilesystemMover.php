<?php declare(strict_types=1);

namespace Symplify\Statie\MigratorJekyll\Filesystem;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\Statie\MigratorJekyll\Command\Reporter\MigrateJekyllReporter;
use function Safe\getcwd;

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
    public function processPaths(array $paths): void
    {
        foreach ($paths as $oldPath => $newPath) {
            if (Strings::contains($oldPath, '*')) { // is match
                $this->processMatchPath($oldPath, $newPath);
            } else {
                $this->processMove($oldPath, $newPath);
            }
        }

        $this->symfonyStyle->success('Files moved');
    }

    private function processMatchPath(string $oldPath, string $newPath): void
    {
        $foundFileInfos = $this->migratorFilesystem->findFilesWithGlob($oldPath);

        foreach ($foundFileInfos as $foundFileInfo) {
            $oldPath = $foundFileInfo->getRealPath();

            // new path is only directory
            $currentPath = rtrim($newPath, '/') . '/' . $foundFileInfo->getRelativeFilePathFromDirectory(getcwd());

            $currentPath = $this->migratorFilesystem->absolutizePath($currentPath);

            $this->migrateJekyllReporter->reportPathOperation('Moved', $oldPath, $currentPath);
            FileSystem::rename($oldPath, $currentPath);
        }
    }

    private function processMove(string $oldPath, string $newPath): void
    {
        $oldPath = $this->migratorFilesystem->absolutizePath($oldPath);
        if (! file_exists($oldPath)) {
            return;
        }

        $newPath = $this->migratorFilesystem->absolutizePath($newPath);
        $this->migrateJekyllReporter->reportPathOperation('Moved', $oldPath, $newPath);
        FileSystem::rename($oldPath, $newPath);
    }
}
