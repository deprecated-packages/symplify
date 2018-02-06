<?php declare(strict_types=1);

namespace Symplify\Monorepo\Worker;

use GitWrapper\GitWorkingCopy;
use Nette\Utils\Strings;

final class RepositoryWorker
{
    public function mergeRepositoryToMonorepoDirectory(string $repository, GitWorkingCopy $gitWorkingCopy): void
    {
        $remoteName = $this->createRepositoryName($repository);
        $this->addRemote($remoteName, $repository, $gitWorkingCopy);

        $gitWorkingCopy->merge($remoteName . '/master', ['allow-unrelated-histories' => true]);
    }

    private function addRemote(string $remoteName, string $repository, GitWorkingCopy $gitWorkingCopy): void
    {
        if ($gitWorkingCopy->hasRemote($remoteName)) {
            return;
        }

        $gitWorkingCopy->addRemote($remoteName, $repository, ['-f' => true]);
    }

    /**
     * This name is needed for git, since it requires [a-zA-Z] string name for remote.
     */
    private function createRepositoryName(string $repository): string
    {
        return Strings::webalize($repository);
    }
}
