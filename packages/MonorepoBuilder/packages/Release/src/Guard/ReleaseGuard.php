<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Guard;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Configuration\Option;
use Symplify\MonorepoBuilder\Exception\Git\InvalidGitVersionException;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\StageAwareInterface;
use Symplify\MonorepoBuilder\Release\Exception\ConfigurationException;
use Symplify\MonorepoBuilder\Split\Git\GitManager;
use function Safe\getcwd;
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
     * @var string[]
     */
    private $stagesToAllowExistingTag = [];

    /**
     * @var GitManager
     */
    private $gitManager;

    /**
     * @param ReleaseWorkerInterface[] $releaseWorkers
     * @param string[] $stagesToAllowExistingTag
     */
    public function __construct(
        GitManager $gitManager,
        array $releaseWorkers,
        bool $isStageRequired,
        array $stagesToAllowExistingTag
    ) {
        $this->gitManager = $gitManager;
        $this->releaseWorkers = $releaseWorkers;
        $this->isStageRequired = $isStageRequired;
        $this->stagesToAllowExistingTag = $stagesToAllowExistingTag;
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

    public function guardVersion(Version $version, ?string $stage): void
    {
        // stage is set and it doesn't need a validatoin
        if ($stage && in_array($stage, $this->stagesToAllowExistingTag, true)) {
            return;
        }

        $this->ensureVersionIsNewerThanLastOne($version);
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

    private function ensureVersionIsNewerThanLastOne(Version $version): void
    {
        $mostRecentVersion = new Version($this->gitManager->getMostRecentTag(getcwd()));
        if ($version->isGreaterThan($mostRecentVersion)) {
            return;
        }

        throw new InvalidGitVersionException(sprintf(
            'Provided version "%s" must be never than the last one: "%s"',
            $version->getVersionString(),
            $mostRecentVersion->getVersionString()
        ));
    }
}
