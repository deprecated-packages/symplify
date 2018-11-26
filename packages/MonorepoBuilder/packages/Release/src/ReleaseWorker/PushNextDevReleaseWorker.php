<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;
use Symplify\MonorepoBuilder\Utils\Utils;
use function Safe\sprintf;

final class PushNextDevReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var Utils
     */
    private $utils;

    public function __construct(ProcessRunner $processRunner, Utils $utils)
    {
        $this->processRunner = $processRunner;
        $this->utils = $utils;
    }

    public function getPriority(): int
    {
        return 50;
    }

    public function work(Version $version): void
    {
        $versionInString = $this->getVersionDev($version);

        $this->processRunner->run(sprintf('git commit -m "open %s"', $versionInString . '-dev'));
    }

    public function getDescription(Version $version): string
    {
        $versionInString = $this->getVersionDev($version);

        return sprintf('Push "%s" open to remote repository', $versionInString);
    }

    private function getVersionDev(Version $version): string
    {
        return $this->utils->getNextAliasFormat($version);
    }
}
