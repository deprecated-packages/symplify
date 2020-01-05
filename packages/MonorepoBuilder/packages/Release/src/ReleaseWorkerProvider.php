<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release;

use Nette\Utils\Strings;
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
    public function __construct(array $releaseWorkers, bool $enableDefaultReleaseWorkers)
    {
        $this->setWorkersAndSortByPriority($releaseWorkers, $enableDefaultReleaseWorkers);
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
            if ($releaseWorker instanceof StageAwareInterface) {
                if ($stage === $releaseWorker->getStage()) {
                    $activeReleaseWorkers[] = $releaseWorker;
                }
            }
        }

        return $activeReleaseWorkers;
    }

    /**
     * @param ReleaseWorkerInterface[] $releaseWorkers
     */
    private function setWorkersAndSortByPriority(array $releaseWorkers, bool $enableDefaultReleaseWorkers): void
    {
        foreach ($releaseWorkers as $releaseWorker) {
            if ($this->shouldSkip($releaseWorker, $enableDefaultReleaseWorkers)) {
                continue;
            }

            $priority = $releaseWorker->getPriority();
            if (isset($this->releaseWorkersByPriority[$priority])) {
                throw new ConflictingPriorityException($releaseWorker, $this->releaseWorkersByPriority[$priority]);
            }

            $this->releaseWorkersByPriority[$priority] = $releaseWorker;
        }

        krsort($this->releaseWorkersByPriority);
    }

    private function shouldSkip(ReleaseWorkerInterface $releaseWorker, bool $enableDefaultReleaseWorkers): bool
    {
        if ($enableDefaultReleaseWorkers) {
            return false;
        }

        return Strings::startsWith(get_class($releaseWorker), 'Symplify\MonorepoBuilder\Release');
    }
}
