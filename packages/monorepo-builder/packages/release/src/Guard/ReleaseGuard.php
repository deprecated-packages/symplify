<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Guard;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Exception\Git\InvalidGitVersionException;
use Symplify\MonorepoBuilder\Git\MostRecentTagResolver;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\StageAwareInterface;
use Symplify\MonorepoBuilder\Release\Exception\ConfigurationException;
use Symplify\MonorepoBuilder\Release\ValueObject\Stage;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

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
     * @var MostRecentTagResolver
     */
    private $mostRecentTagResolver;

    /**
     * @param ReleaseWorkerInterface[] $releaseWorkers
     */
    public function __construct(
        ParameterProvider $parameterProvider,
        MostRecentTagResolver $mostRecentTagResolver,
        array $releaseWorkers
    ) {
        $this->releaseWorkers = $releaseWorkers;
        $this->isStageRequired = $parameterProvider->provideBoolParameter(Option::IS_STAGE_REQUIRED);
        $this->stagesToAllowExistingTag = $parameterProvider->provideArrayParameter(
            Option::STAGES_TO_ALLOW_EXISTING_TAG
        );
        $this->mostRecentTagResolver = $mostRecentTagResolver;
    }

    public function guardRequiredStageOnEmptyStage(): void
    {
        // there are no stages → nothing to filter by
        if ($this->getStages() === []) {
            return;
        }

        // stage is optional → all right
        if (! $this->isStageRequired) {
            return;
        }

        // stage is required → show options
        throw new ConfigurationException(sprintf(
            'Set "--%s <name>" option first. Pick one of: "%s"',
            Option::STAGE,
            implode('", "', $this->getStages())
        ));
    }

    public function guardStage(string $stage): void
    {
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

    public function guardVersion(Version $version, string $stage): void
    {
        // stage is set and it doesn't need a validation
        if ($stage !== Stage::MAIN && in_array($stage, $this->stagesToAllowExistingTag, true)) {
            return;
        }

        $this->ensureVersionIsNewerThanLastOne($version);
    }

    /**
     * @return string[]
     */
    private function getStages(): array
    {
        if ($this->stages !== []) {
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
        $mostRecentVersion = $this->mostRecentTagResolver->resolve(getcwd());

        // no tag yet
        if ($mostRecentVersion === null) {
            return;
        }

        // normalize to workaround phar-io bug
        $mostRecentVersion = strtolower($mostRecentVersion);

        // validation
        $mostRecentVersion = new Version($mostRecentVersion);
        if ($version->isGreaterThan($mostRecentVersion)) {
            return;
        }

        throw new InvalidGitVersionException(sprintf(
            'Provided version "%s" must be greater than the last one: "%s"',
            $version->getVersionString(),
            $mostRecentVersion->getVersionString()
        ));
    }
}
