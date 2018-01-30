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
                $localSubdirectory,
                $remoteRepository,
                $theMostRecentTag
            );
        }
    }

    private function splitLocalSubdirectoryToGitRepositoryWithTag(
        string $localSubdirectory,
        string $remoteGitRepository,
        ?string $theMostRecentTag = null
    ): void {
        // ...
    }

    private function getMostRecentTag(GitWorkingCopy $gitWorkingCopy): string
    {
        $tags = $gitWorkingCopy->tag('-l');
        $tagList = explode(PHP_EOL, trim($tags));

        return (string) array_pop($tagList);
    }
}
