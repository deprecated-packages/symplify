<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Configuration;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\ChangelogLinker\Analyzer\IdsAnalyzer;
use Symplify\ChangelogLinker\Github\GithubApi;

final class HighestMergedIdResolver
{
    /**
     * @var int
     */
    private const FALLBACK_ID = 1;

    /**
     * @var IdsAnalyzer
     */
    private $idsAnalyzer;

    /**
     * @var GithubApi
     */
    private $githubApi;

    public function __construct(IdsAnalyzer $idsAnalyzer, GithubApi $githubApi)
    {
        $this->idsAnalyzer = $idsAnalyzer;
        $this->githubApi = $githubApi;
    }

    public function resolveFromInputAndChangelogContent(InputInterface $input, string $content): int
    {
        /** @var string|int|null $sinceId */
        $sinceId = $input->getOption(Option::SINCE_ID);
        if ($sinceId !== null) {
            return (int) $sinceId;
        }

        /** @var string $baseBranch */
        $baseBranch = $input->getOption(Option::BASE_BRANCH);
        if ($baseBranch !== null) {
            $resolvedId = $this->resolveFromChangelogContentAndBranch($content, $baseBranch);
            if ($resolvedId !== null) {
                return $resolvedId;
            }

            return self::FALLBACK_ID;
        }

        return $this->idsAnalyzer->getHighestIdInChangelog($content);
    }

    private function resolveFromChangelogContentAndBranch(string $content, string $branch): ?int
    {
        $allIdsInChangelog = $this->idsAnalyzer->getAllIdsInChangelog($content);
        if ($allIdsInChangelog === null) {
            return null;
        }

        rsort($allIdsInChangelog);
        foreach ($allIdsInChangelog as $id) {
            $idInt = (int) $id;
            if ($this->githubApi->isPullRequestMergedToBaseBranch($idInt, $branch)) {
                return $idInt;
            }
        }
        return null;
    }
}
