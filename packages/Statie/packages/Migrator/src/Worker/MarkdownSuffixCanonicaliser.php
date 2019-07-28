<?php declare(strict_types=1);

namespace Symplify\Statie\Migrator\Worker;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\Statie\Migrator\Contract\MigratorWorkerInterface;
use Symplify\Statie\Migrator\Filesystem\MigratorFilesystem;

/**
 * Canonicalise suffix of markdown files
 *
 * Statie requires that markdown files are suffixed with .md, jekyll allows a variety of suffixes. Files using the
 * suffixes from jekyll that Statie does not, are canonicalised here.
 */
final class MarkdownSuffixCanonicaliser implements MigratorWorkerInterface
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
        $markdownFileInfos = $this->migratorFilesystem->getIncorrectlyNamedMarkdownFiles($sourceDirectory);

        foreach ($markdownFileInfos as $markdownFileInfo) {
            $oldPath = $markdownFileInfo->getRealPath();

            $newPath = Strings::replace($oldPath, '#\.(markdown|mkdown|mkdn|mkd)$#', '.md');
            if ($oldPath === $newPath) {
                continue;
            }

            FileSystem::rename($oldPath, $newPath);

            $this->symfonyStyle->note(sprintf('File "%s" suffix renamed to "%s"', $oldPath, $newPath));
        }

        $this->symfonyStyle->success('Suffixes changed to .md');
    }
}
