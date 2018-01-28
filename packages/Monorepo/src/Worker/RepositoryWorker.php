<?php declare(strict_types=1);

namespace Symplify\Monorepo\Worker;

use GitWrapper\GitWorkingCopy;
use GitWrapper\GitWrapper;
use Symfony\Component\Console\Style\SymfonyStyle;

final class RepositoryWorker
{
    /**
     * @var GitWrapper
     */
    private $gitWrapper;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(GitWrapper $gitWrapper, SymfonyStyle $symfonyStyle)
    {
        $this->gitWrapper = $gitWrapper;
        $this->symfonyStyle = $symfonyStyle;
    }

    private function getRepositoryForDirectory(string $monorepoDirectory): GitWorkingCopy
    {
        $gitWorkingCopy = $this->gitWrapper->workingCopy($monorepoDirectory);
        // be sure it's git repository
        $gitWorkingCopy->init();

        return $gitWorkingCopy;
    }

    public function fetchAndMergeRepository(string $gitRepository, string $monorepoDirectory): void
    {
        $gitWorkingCopy = $this->getRepositoryForDirectory($monorepoDirectory);

        $remoteName = md5($gitRepository);
        $this->addRemote($gitRepository, $gitWorkingCopy, $remoteName);

        $gitWorkingCopy->merge($remoteName . '/master', ['allow-unrelated-histories' => true]);
        $this->symfonyStyle->success(sprintf(
            'Remote repository "%s" was merged in "%s"',
            $gitRepository,
            $monorepoDirectory
        ));
    }

    private function addRemote(string $gitRepository, GitWorkingCopy $gitWorkingCopy, string $remoteName): void
    {
        if ($gitWorkingCopy->hasRemote($remoteName)) {
            $this->symfonyStyle->note(sprintf('Remote repository "%s" is already added', $gitRepository));
            return;
        }

        $gitWorkingCopy->addRemote($remoteName, $gitRepository, ['-f' => true]);

        $this->symfonyStyle->success(sprintf('Remote repository "%s" was added', $gitRepository));
    }
}
