<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Process;

use Symfony\Component\Process\Process;

final class SplitProcessInfoFactory
{
    public function createFromProcessLocalDirectoryAndRemoteRepository(
        Process $process,
        string $localDirectory,
        string $remoteRepository
    ): SplitProcessInfo {
        return new SplitProcessInfo($process, $localDirectory, $remoteRepository);
    }
}
