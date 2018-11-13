<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker;

use PharIo\Version\Version;

interface ReleaseWorkerInterface
{
    /**
     * 1 line description of what this worker does, in command! form, e.g.:
     * - "Add new tag"
     */
    public function getDescription(): string;

    /**
     * Higher first
     */
    public function getPriority(): int;

    public function work(Version $version): void;
}
