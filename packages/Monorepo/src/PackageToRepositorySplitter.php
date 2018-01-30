<?php declare(strict_types=1);

namespace Symplify\Monorepo;

use GitWrapper\GitWorkingCopy;
use Symfony\Component\Console\Style\SymfonyStyle;

final class PackageToRepositorySplitter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    /**
     * @param mixed[] $splitConfig
     */
    public function splitDirectoriesToRepositories(GitWorkingCopy $gitWorkingCopy, array $splitConfig): void
    {
        $theMostRecentTag = $this->getMostRecentTag($gitWorkingCopy);

        foreach ($splitConfig as $localSubdirectory => $remoteRepository) {
            $this->splitLocalSubdirectoryToRepositoryWithTag(
                $gitWorkingCopy,
                $localSubdirectory,
                $remoteRepository,
                $theMostRecentTag
            );
        }
    }

    private function splitLocalSubdirectoryToRepositoryWithTag(
        GitWorkingCopy $gitWorkingCopy,
        string $localSubdirectory,
        string $remoteRepository,
        string $theMostRecentTag
    ): void {
        // @todo validate local directory
        // @todo validate remote repository

        $options = [
            'publish',
            '--heads=master',
            sprintf('--tags=%s', $theMostRecentTag),
            sprintf('%s:%s', $localSubdirectory, $remoteRepository),
        ];

        $gitWorkingCopy->run('subsplit', $options);

        $this->symfonyStyle->success(sprintf(
            'Package "%s" was split to "%s" repository',
            $localSubdirectory,
            $remoteRepository
        ));
    }

    private function getMostRecentTag(GitWorkingCopy $gitWorkingCopy): string
    {
        $tags = $gitWorkingCopy->tag('-l');
        $tagList = explode(PHP_EOL, trim($tags));

        return (string) array_pop($tagList);
    }
}
