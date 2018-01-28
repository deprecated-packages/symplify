<?php declare(strict_types=1);

namespace Symplify\Monorepo\Worker;

use GitWrapper\GitWrapper;

/**
 * Mimics run.sh
 */
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

        // 2. clean current repository
        // clean git but - maybe own worker?
        $gitWorkingCopy->rm('.', ['r' => true]);
        // remove all files but this script
        // this should remove all files that could be in conflict
        $gitWorkingCopy->checkout('HEAD', '--', 'packages', 'vendor');

        // ref: http://blog.bfitz.us/?p=1811
        // wip
        // .gitignore has to moved as well, so it's not in conflict
        // git update-index --assume-unchanged composer.lock (gitiginore might be needed to return later)

        // workaround - add /vendor as well for now
        $gitWorkingCopy->add('*');

        // removing .gitignore causes to see /vendor
        $gitWorkingCopy->commit( 'staging');

        // 3. merge
        $gitWorkingCopy->merge($remoteName . '/master', ['allow-unrelated-histories' => true]);
    }
}
