<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;

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

    public function getPriority(): int
    {
        return 400;
    }

    public function work(Version $version): void
    {
        $this->processRunner->run('git add . && git commit -m "prepare release" && git push origin master');
        $this->processRunner->run('git tag ' . $version->getVersionString());
    }

    public function getDescription(Version $version): string
    {
        return sprintf('Add local tag "%s"', $version->getVersionString());
    }
}
