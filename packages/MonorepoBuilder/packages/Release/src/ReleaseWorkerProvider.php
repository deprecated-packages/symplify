<?php declare(strict_types=1);

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
    public function provideByStage(?string $stage): array
    {
        if ($stage === null) {
            return $this->releaseWorkers;
        }

        $activeReleaseWorkers = [];
        foreach ($this->releaseWorkers as $releaseWorker) {
            if ($releaseWorker instanceof StageAwareInterface) {
                if ($stage === $releaseWorker->getStage()) {
                    $activeReleaseWorkers[] = $releaseWorker;
                }
            }
        }

        return $activeReleaseWorkers;
    }
}
