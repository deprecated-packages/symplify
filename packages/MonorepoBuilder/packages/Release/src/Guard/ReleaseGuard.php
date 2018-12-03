<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Guard;

use Symplify\MonorepoBuilder\Configuration\Option;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\StageAwareInterface;
use Symplify\MonorepoBuilder\Release\Exception\ConfigurationException;
use function Safe\sprintf;

final class ReleaseGuard
{
    /**
     * @var bool
     */
    private $isStageRequired = false;

    /**
     * @var ReleaseWorkerInterface[]
     */
    private $releaseWorkers = [];

    /**
     * @var string[]
     */
    private $stages = [];

    /**
     * @param ReleaseWorkerInterface[] $releaseWorkers
     */
    public function __construct(array $releaseWorkers, bool $isStageRequired)
    {
        $this->releaseWorkers = $releaseWorkers;
        $this->isStageRequired = $isStageRequired;
    }

    public function guardStage(?string $stage): void
    {
        if ($stage === null) {
            // there are no stages → nothing to filter by
            if ($this->getStages() === []) {
                return;
            }

            // stage is optional → all right
            if ($this->isStageRequired === false) {
                return;
            }

            // stage is required → show options
            throw new ConfigurationException(sprintf(
                'Set "--%s <name>" option first. Pick one of: "%s"',
                Option::STAGE,
                implode('", "', $this->getStages())
            ));
        }

        // stage is correct
        if (in_array($stage, $this->getStages(), true)) {
            return;
        }

        // stage has invalid value
        throw new ConfigurationException(sprintf(
            'Stage "%s" was not found. Pick one of: "%s"',
            $stage,
            implode('", "', $this->getStages())
        ));
    }

    /**
     * @return string[]
     */
    private function getStages(): array
    {
        if ($this->stages) {
            return $this->stages;
        }

        $stages = [];
        foreach ($this->releaseWorkers as $releaseWorker) {
            if ($releaseWorker instanceof StageAwareInterface) {
                $stages[] = $releaseWorker->getStage();
            }
        }

        $this->stages = array_unique($stages);

        return $this->stages;
    }
}
