<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release;

use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\StageAwareInterface;
use Symplify\MonorepoBuilder\Release\Exception\ConflictingPriorityException;

final class ReleaseWorkerProvider
{
    /**
     * @var ReleaseWorkerInterface[]
     */
    private $releaseWorkersByPriority = [];

    /**
     * @param ReleaseWorkerInterface[] $releaseWorkers
     */
    public function __construct(array $releaseWorkers)
    {
        $this->setWorkersAndSortByPriority($releaseWorkers);
    }

    /**
     * @return ReleaseWorkerInterface[]
     */
    public function provideByStage(?string $stage): array
    {
        if ($stage === null) {
            return $this->releaseWorkersByPriority;
        }

        $activeReleaseWorkers = [];
        foreach ($this->releaseWorkersByPriority as $releaseWorker) {
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

    /**
     * @param ReleaseWorkerInterface[] $releaseWorkers
     */
    private function setWorkersAndSortByPriority(array $releaseWorkers): void
    {
        foreach ($releaseWorkers as $releaseWorker) {
            $priority = $releaseWorker->getPriority();
            if (isset($this->releaseWorkersByPriority[$priority])) {
                throw new ConflictingPriorityException($releaseWorker, $this->releaseWorkersByPriority[$priority]);
            }

            $this->releaseWorkersByPriority[$priority] = $releaseWorker;
        }

        krsort($this->releaseWorkersByPriority);
    }
}
