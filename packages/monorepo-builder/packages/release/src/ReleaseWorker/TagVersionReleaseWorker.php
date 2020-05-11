<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;
use Throwable;

final class TagVersionReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    public function __construct(ProcessRunner $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    public function work(Version $version): void
    {
        try {
            $this->processRunner->run('git add . && git commit -m "prepare release" && git push origin master');
        } catch (Throwable $throwable) {
            // nothing to commit
        }

        $this->processRunner->run('git tag ' . $version->getVersionString());
    }

    public function getDescription(Version $version): string
    {
        return sprintf('Add local tag "%s"', $version->getVersionString());
    }
}
