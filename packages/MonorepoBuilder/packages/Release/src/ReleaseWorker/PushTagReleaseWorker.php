<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

final class PushTagReleaseWorker implements ReleaseWorkerInterface

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
        return 300;
    }

    public function work(Version $version, bool $isDryRun): void
    {
        $this->symfonyStyle->note(sprintf('Pushing tag "%s"', $version->getVersionString()));

        $process = new Process('git push --tags');
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
