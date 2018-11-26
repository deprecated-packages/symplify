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
        return 280;
    }

    public function work(Version $version): void
    {
        $versionInString = $this->utils->getRequiredNextFormat($version);

        $this->processRunner->run(sprintf('git commit -m "open %s"', $versionInString . '-dev'));
    }

    public function getDescription(Version $version): string
    {
        return sprintf('Push "%s" tag to remote repository', $version->getVersionString());
    }
}
