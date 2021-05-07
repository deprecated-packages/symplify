<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Throwable;

final class TagVersionReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var string
     */
    private $branchName;

    public function __construct(ProcessRunner $processRunner, ParameterProvider $parameterProvider)
    {
        $this->processRunner = $processRunner;
        $this->branchName = $parameterProvider->provideStringParameter(Option::DEFAULT_BRANCH_NAME);
    }

    public function work(Version $version): void
    {
        try {
            $gitAddCommitCommand = sprintf(
                'git add . && git commit -m "prepare release" && git push origin "%s"',
                $this->branchName
            );

            $this->processRunner->run($gitAddCommitCommand);
        } catch (Throwable $throwable) {
            // nothing to commit
        }

        $this->processRunner->run('git tag ' . $version->getOriginalString());
    }

    public function getDescription(Version $version): string
    {
        return sprintf('Add local tag "%s"', $version->getOriginalString());
    }
}
