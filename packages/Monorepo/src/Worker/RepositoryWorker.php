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

    public function fetchAndMergeRepository(string $gitRepository, string $monorepoDirectory): void
    {
        $gitWorkingCopy = $this->gitWrapper->workingCopy($monorepoDirectory);

        $remoteName = md5($gitRepository);
        if (! $gitWorkingCopy->hasRemote($remoteName)) {
            $gitWorkingCopy->addRemote($remoteName, $gitRepository, ['-f' => true]);
        }

        $gitWorkingCopy->merge($remoteName . '/master', ['allow-unrelated-histories' => true]);
    }
}
