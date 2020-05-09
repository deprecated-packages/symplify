<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release;

use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\StageAwareInterface;

/**
 * @see \Symplify\MonorepoBuilder\Release\Tests\ReleaseWorkerProvider\ReleaseWorkerProviderTest
 */
final class ReleaseWorkerProvider
{
    /**
     * @var ReleaseWorkerInterface[]
     */
    private $releaseWorkers = [];

    /**
     * @param ReleaseWorkerInterface[] $releaseWorkers
     */
    public function __construct(array $releaseWorkers)
    {
        $this->releaseWorkers = $releaseWorkers;
    }

    /**
     * @return ReleaseWorkerInterface[]
     */
    public function provide(): array
    {
        return $this->releaseWorkers;
    }

    /**
     * @return ReleaseWorkerInterface[]
     */
    public function provideByStage(string $stage): array
    {
        $activeReleaseWorkers = [];
        foreach ($this->releaseWorkers as $releaseWorker) {
            if (! $releaseWorker instanceof StageAwareInterface) {
                continue;
            }

            if ($stage !== $releaseWorker->getStage()) {
                continue;
            }

            $activeReleaseWorkers[] = $releaseWorker;
        }

        return $activeReleaseWorkers;
    }
}
