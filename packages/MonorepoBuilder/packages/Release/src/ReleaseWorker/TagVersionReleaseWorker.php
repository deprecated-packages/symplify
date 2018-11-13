<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

final class TagVersionReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    public function getPriority(): int
    {
        return 400;
    }

    public function work(Version $version, bool $isDryRun): void
    {
        $this->symfonyStyle->note(sprintf('Tagging version "%s"', $version->getVersionString()));

        // commit previous changes
        $process = new Process('git add . && git commit -m "prepare release" && git push origin master');
        if ($isDryRun) {
            $this->symfonyStyle->note('Would run: ' . $process->getCommandLine());
        } else {
            $process->run();

            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        }

        $process = new Process(sprintf('git tag %s', $version->getVersionString()));
        if ($isDryRun) {
            $this->symfonyStyle->note('Would run: ' . $process->getCommandLine());
        } else {
            $process->run();

            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        }

        $this->symfonyStyle->success('Done!');
    }
}
