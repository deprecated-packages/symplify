<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker;

use PharIo\Version\Version;

interface ReleaseWorkerInterface
{
    /**
     * Higher first
     */
    public function getPriority(): int;

    public function work(Version $version, bool $isDryRun): void;
}
