<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class GuardOnDefaultBranchReleaseWorker implements ReleaseWorkerInterface
{
    private string $branchName;

    public function __construct(
        private ProcessRunner $processRunner,
        ParameterProvider $parameterProvider
    ) {
        $this->branchName = $parameterProvider->provideStringParameter(Option::DEFAULT_BRANCH_NAME);
    }

    public function work(Version $version): void
    {
        $currentBranchName = trim($this->processRunner->run('git branch --show-current'));
        if ($currentBranchName !== $this->branchName) {
            throw new ShouldNotHappenException(sprintf(
                'Switch from branch "%s" to "%s" before doing the release',
                $currentBranchName,
                $this->branchName
            ));
        }
    }

    public function getDescription(Version $version): string
    {
        return 'Check we are on the default branch, to avoid commit/push to a different branch';
    }
}
