<?php declare(strict_types=1);

namespace Symplify\Statie\Migrator\Worker;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\Statie\Migrator\Contract\MigratorWorkerInterface;
use Symplify\Statie\Migrator\Filesystem\MigratorFilesystem;

final class PostIdsAdder implements MigratorWorkerInterface
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
        $postFileInfos = $this->migratorFilesystem->findPostFiles($sourceDirectory . '/_posts');
        $id = 1;

        foreach ($postFileInfos as $postFileInfo) {
            if (Strings::match($postFileInfo->getContents(), '#^\-\-\-\s+id:#')) {
                // has ID
                continue;
            }

            $oldContent = $postFileInfo->getContents();
            $newContent = Strings::replace($oldContent, '#^\-\-\-#', '---' . PHP_EOL . 'id: ' . $id);
            if ($newContent === $oldContent) {
                continue;
            }

            // save file
            FileSystem::write($postFileInfo->getRealPath(), $newContent);

            $fileRelativePath = $postFileInfo->getRelativeFilePathFromDirectory($workingDirectory);
            $this->symfonyStyle->note(sprintf('Post id "%d" was completed to "%s" file', $id, $fileRelativePath));

            ++$id;
        }

        $this->symfonyStyle->success('Ids were completed to posts');
    }
}
