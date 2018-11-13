<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

final class PushTagReleaseWorker implements ReleaseWorkerInterface
{
    public function getPriority(): int
    {
        return 300;
    }

    public function work(Version $version): void
    {
        $process = new Process('git push --tags');
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    public function getDescription(): string
    {
        return 'Push tag to remote repository';
    }
}
