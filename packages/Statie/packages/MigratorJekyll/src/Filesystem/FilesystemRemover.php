<?php declare(strict_types=1);

namespace Symplify\Statie\MigratorJekyll\Filesystem;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\Statie\MigratorJekyll\Command\Reporter\MigrateJekyllReporter;

final class FilesystemRemover
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
     * @param string[] $paths
     */
    public function processPaths(array $paths): void
    {
        foreach ($paths as $path) {
            $absolutePath = $this->migratorFilesystem->absolutizePath($path);
            if (! file_exists($absolutePath)) {
                continue;
            }

            $this->migrateJekyllReporter->reportPathOperation('Deleted', $absolutePath, '/source');
            FileSystem::delete($absolutePath);
        }

        $this->symfonyStyle->success('Files removed');
    }
}
