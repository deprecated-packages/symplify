<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\ChangeTree;

final class ChangeResolver
{
    /**
     * @var ChangeFactory
     */
    private $changeFactory;

    /**
     * @var ChangeSorter
     */
    private $changeSorter;

    public function __construct(ChangeFactory $changeFactory, ChangeSorter $changeSorter)
    {
        $this->changeFactory = $changeFactory;
        $this->changeSorter = $changeSorter;
    }

    /**
     * @param mixed[] $pullRequests
     * @return Change[]
     */
    public function resolveSortedChangesFromPullRequestsWithSortPriority(
        array $pullRequests,
        ?string $sortPriority
    ): array {
        $changes = [];
        foreach ($pullRequests as $pullRequest) {
            $changes[] = $this->changeFactory->createFromPullRequest($pullRequest);
        }

        $sortedChanges = $this->changeSorter->sortByCategoryAndPackage($changes, $sortPriority);

        return $this->changeSorter->sortByTags($sortedChanges);
    }
}
