<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

final class TagVersionReleaseWorker implements ReleaseWorkerInterface
{
    public function getPriority(): int
    {
        return 400;
    }

    public function work(Version $version): void
    {
        // commit previous changes
        $process = new Process('git add . && git commit -m "prepare release" && git push origin master');
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $process = new Process(sprintf('git tag %s', $version->getVersionString()));
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    public function getDescription(): string
    {
        return 'Add local tag';
    }
}
