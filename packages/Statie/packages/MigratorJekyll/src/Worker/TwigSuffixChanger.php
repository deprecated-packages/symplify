<?php declare(strict_types=1);

namespace Symplify\Statie\MigratorJekyll\Worker;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\Statie\MigratorJekyll\Contract\MigratorJekyllWorkerInterface;
use Symplify\Statie\MigratorJekyll\Filesystem\MigratorFilesystem;
use function Safe\getcwd;
use function Safe\sprintf;

final class TwigSuffixChanger implements MigratorJekyllWorkerInterface
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

    public function processSourceDirectory(string $sourceDirectory): void
    {
        $twigFileInfos = $this->migratorFilesystem->getPossibleTwigFiles($sourceDirectory);

        foreach ($twigFileInfos as $twigFileInfo) {
            // 1. replace "include *.html" with "include *.twig"
            $newContent = Strings::replace(
                $twigFileInfo->getContents(),
                '#({% include )(.*?).html( %})#',
                '$1$2.twig$3'
            );

            // 2. save to `*.twig` file
            $oldPath = $twigFileInfo->getRelativeFilePathFromDirectory(getcwd());
            $newPath = Strings::replace($oldPath, '#\.(html|xml|md)$#', '.twig');

            FileSystem::write($newPath, $newContent);

            // remove old file
            if ($oldPath !== $newPath) {
                FileSystem::delete($oldPath);
            }

            $this->symfonyStyle->note(sprintf('File "%s" changed to "%s" including paths in it', $oldPath, $newPath));
        }

        $this->symfonyStyle->success('Suffixes changed to .twig');
    }
}
