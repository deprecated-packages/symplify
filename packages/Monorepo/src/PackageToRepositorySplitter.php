<?php declare(strict_types=1);

namespace Symplify\Monorepo;

use GitWrapper\GitWorkingCopy;

final class PackageToRepositorySplitter
{
    /**
     * @param mixed[] $splitConfig
     */
    public function splitDirectoriesToRepositories(GitWorkingCopy $gitWorkingCopy, array $splitConfig): void
    {
        $theMostRecentTag = $this->getMostRecentTag($gitWorkingCopy);

        foreach ($splitConfig as $localSubdirectory => $remoteRepository) {
            $this->splitLocalSubdirectoryToGitRepositoryWithTag(
                $gitWorkingCopy,
                $localSubdirectory,
                $remoteRepository,
                $theMostRecentTag
            );
        }
    }

    private function splitLocalSubdirectoryToGitRepositoryWithTag(
        GitWorkingCopy $gitWorkingCopy,
        string $localSubdirectory,
        string $remoteGitRepository,
        string $theMostRecentTag
    ): void {
        $gitWorkingCopy->run('subsplit', [
            'publish',
            'heads' => 'master',
            'tags' => $theMostRecentTag,
            sprintf('%s:%s', $localSubdirectory, $remoteGitRepository),
        ]);
    }

    private function getMostRecentTag(GitWorkingCopy $gitWorkingCopy): string
    {
        $tags = $gitWorkingCopy->tag('-l');
        $tagList = explode(PHP_EOL, trim($tags));

        return (string) array_pop($tagList);
    }
}
