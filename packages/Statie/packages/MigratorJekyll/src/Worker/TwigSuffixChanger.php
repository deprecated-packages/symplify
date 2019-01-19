<?php declare(strict_types=1);

namespace Symplify\Statie\MigratorJekyll\Worker;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\Statie\MigratorJekyll\Contract\MigratorJekyllWorkerInterface;
use Symplify\Statie\MigratorJekyll\Filesystem\MigratorFilesystem;
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

    public function processSourceDirectory(string $sourceDirectory, string $workingDirectory): void
    {
        $twigFileInfos = $this->migratorFilesystem->getPossibleTwigFiles($sourceDirectory, $workingDirectory);

        foreach ($twigFileInfos as $twigFileInfo) {
            // replace "include *.html" with "include *.twig"
            $newContent = Strings::replace(
                $twigFileInfo->getContents(),
                '#({% include )(.*?).html( %})#',
                '$1\'$2.twig\'$3'
            );

            // replace "remove" with "replace" - @see https://regex101.com/r/iTaipb/2
            $newContent = Strings::replace(
                $newContent,
                '#(\|(\s+)?)(?<filter>remove):\s*(?<value>.*?)\s*(\||}})#',
                '$1replace($4, \'\')$5'
            );

            // replace "contains" with "in" - @see https://regex101.com/r/iTaipb/3
            $newContent = Strings::replace(
                $newContent,
                '#({(%|{).*?)\s*(?<value>\w+)\s*contains\s*(?<needle>.*?)(\s)#',
                '$1 $4 in $3$5'
            );

            // replace "assign" with "set"
            $newContent = Strings::replace($newContent, '#({%)\s*assign\s*(.*?)#', '$1 set $2');
            $newContent = Strings::replace($newContent, '#{% capture (.*?) endcapture %}#', '{% set $1 endset %}');

            // save to `*.twig` file
            $oldPath = $twigFileInfo->getRelativeFilePathFromDirectory($workingDirectory);

            $newPath = Strings::replace($oldPath, '#\.(html|xml|md)$#', '.twig');
            $newPath = $this->migratorFilesystem->absolutizePath($newPath, $workingDirectory);

            FileSystem::write($newPath, $newContent);

            $oldPath = $this->migratorFilesystem->absolutizePath($oldPath, $workingDirectory);

            // remove old file
            if ($oldPath !== $newPath) {
                FileSystem::delete($oldPath);
            }

            $this->symfonyStyle->note(sprintf('File "%s" changed to "%s" including paths in it', $oldPath, $newPath));
        }

        $this->symfonyStyle->success('Suffixes changed to .twig');
    }
}
