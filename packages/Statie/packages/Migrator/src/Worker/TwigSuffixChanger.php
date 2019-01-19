<?php declare(strict_types=1);

namespace Symplify\Statie\Migrator\Worker;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\Statie\Migrator\Contract\MigratorWorkerInterface;
use Symplify\Statie\Migrator\Filesystem\MigratorFilesystem;
use function Safe\sprintf;

final class TwigSuffixChanger implements MigratorWorkerInterface
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var MigratorFilesystem
     */
    private $migratorFilesystem;

    public function __construct(SymfonyStyle $symfonyStyle, MigratorFilesystem $migratorFilesystem)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->migratorFilesystem = $migratorFilesystem;
    }

    public function processSourceDirectory(string $sourceDirectory, string $workingDirectory): void
    {
        $twigFileInfos = $this->migratorFilesystem->getPossibleTwigFiles($sourceDirectory, $workingDirectory);

        foreach ($twigFileInfos as $twigFileInfo) {
            $oldPath = $twigFileInfo->getRealPath();

            $newPath = Strings::replace($oldPath, '#\.(html|xml|md)$#', '.twig');
            if ($oldPath === $newPath) {
                continue;
            }

            FileSystem::rename($oldPath, $newPath);

            $this->symfonyStyle->note(sprintf('File "%s" suffix renamed to "%s"', $oldPath, $newPath));
        }

        $this->symfonyStyle->success('Suffixes changed to .twig');
    }
}
