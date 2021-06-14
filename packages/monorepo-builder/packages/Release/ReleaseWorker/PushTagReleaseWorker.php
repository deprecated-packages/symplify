<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;

final class PushTagReleaseWorker implements ReleaseWorkerInterface
{
    public function __construct(
        private ProcessRunner $processRunner
    ) {
    }

    public function work(Version $version): void
    {
        $this->processRunner->run('git push --tags');
    }

    public function getDescription(Version $version): string
    {
        return sprintf('Push "%s" tag to remote repository', $version->getVersionString());
    }
}
