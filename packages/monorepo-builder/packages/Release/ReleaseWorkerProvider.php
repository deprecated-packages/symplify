<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release;

use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\StageAwareInterface;
use Symplify\MonorepoBuilder\Release\ValueObject\Stage;

/**
 * @see \Symplify\MonorepoBuilder\Tests\Release\ReleaseWorkerProvider\ReleaseWorkerProviderTest
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
     * @return ReleaseWorkerInterface[]|StageAwareInterface[]
     */
    public function provideByStage(string $stage): array
    {
        if ($stage === Stage::MAIN) {
            return $this->releaseWorkers;
        }

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
