<?php declare(strict_types=1);

namespace Symplify\Statie\Migrator\Filesystem;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\Statie\Migrator\Command\Reporter\MigrateReporter;

final class FilesystemRemover
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
     * @param string[] $paths
     */
    public function processPaths(string $workingDirectory, array $paths): void
    {
        foreach ($paths as $path) {
            $absolutePath = $this->migratorFilesystem->absolutizePath($path, $workingDirectory);
            if (! file_exists($absolutePath)) {
                continue;
            }

            $this->migrateReporter->reportPathOperation('Deleted', $absolutePath);
            FileSystem::delete($absolutePath);
        }

        $this->symfonyStyle->success('Files removed');
    }
}
