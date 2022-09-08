<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Git;

use Symplify\MonorepoBuilder\Contract\Git\TagResolverInterface;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunnerInterface;

final class MostRecentTagForCurrentBranchResolver implements TagResolverInterface
{
    public const COMMAND = ['git', 'describe', '--tags', '--abbrev=0'];

    public function __construct(
        private ProcessRunnerInterface $processRunner,
    ) {
    }

    public function resolve(string $gitDirectory): ?string
    {
        $newestTagForCurrentBranch = trim($this->processRunner->run(self::COMMAND, $gitDirectory));

        if ("" === $newestTagForCurrentBranch) {
            return null;
        }

        return $newestTagForCurrentBranch;
    }
}
