<?php declare(strict_types=1);

namespace Symplify\Monorepo;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Symplify\Monorepo\Configuration\RepositoryGuard;
use Symplify\Monorepo\Exception\Worker\PackageToRepositorySplitException;

final class PackageToRepositorySplitter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var RepositoryGuard
     */
    private $repositoryGuard;

    public function __construct(SymfonyStyle $symfonyStyle, RepositoryGuard $repositoryGuard)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->repositoryGuard = $repositoryGuard;
    }

    /**
     * @param mixed[] $splitConfig
     */
    public function splitDirectoriesToRepositories(array $splitConfig): void
    {
        $theMostRecentTag = $this->getMostRecentTag();

        foreach ($splitConfig as $localSubdirectory => $remoteRepository) {
            $process = $this->createSubsplitPublishProcess($theMostRecentTag, $localSubdirectory, $remoteRepository);
            $this->symfonyStyle->note('Running: ' . $process->getCommandLine());

            $process->start();
            while ($process->isRunning()) {
                // waiting for process to finish
                $output = trim($process->getOutput());
                if ($output) {
                    $output = preg_replace('#(\r?\n){2,}#', PHP_EOL, $output);
                    $this->symfonyStyle->writeln($output);
                }
            }

            if ($process->isSuccessful()) {
                $this->symfonyStyle->success(trim($process->getOutput()));
            } else {
                throw new PackageToRepositorySplitException($process->getErrorOutput());
            }
        }
    }

    private function getMostRecentTag(): string
    {
        $process = new Process('git tag -l --sort=committerdate');
        $process->run();
        $tags = $process->getOutput();
        $tagList = explode(PHP_EOL, trim($tags));

        return (string) array_pop($tagList);
    }

    private function createSubsplitPublishProcess(
        string $theMostRecentTag,
        string $localSubdirectory,
        string $remoteRepository
    ): Process {
        $this->repositoryGuard->ensureIsRepository($remoteRepository);

        $commandLine = sprintf(
            'git subsplit publish --heads=master --tags=%s %s',
            $theMostRecentTag,
            sprintf('%s:%s', $localSubdirectory, $remoteRepository)
        );

        return new Process($commandLine, null, null, null, null);
    }
}
