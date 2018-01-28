<?php declare(strict_types=1);

namespace Symplify\Monorepo\Worker;

use GitWrapper\GitWrapper;

final class RepositoryWorker
{
    /**
     * @var GitWrapper
     */
    private $gitWrapper;

    public function __construct(GitWrapper $gitWrapper)
    {
        $this->gitWrapper = $gitWrapper;
    }

    public function fetchAndMergeRepository(string $gitRepository): void
    {
        $gitWorkingCopy = $this->gitWrapper->workingCopy(getcwd());

        // 1. add remote repository
        $remoteName = md5($gitRepository);
        if (! $gitWorkingCopy->hasRemote($remoteName)) {
            $gitWorkingCopy->addRemote($remoteName, $gitRepository, ['-f' => true]);
        }

        // 3. merge
        $gitWorkingCopy->merge($remoteName . '/master', ['allow-unrelated-histories' => true]);
    }
}
