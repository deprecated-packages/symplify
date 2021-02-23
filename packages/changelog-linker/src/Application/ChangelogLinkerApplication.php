<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Application;

use Symplify\ChangelogLinker\ChangelogDumper;
use Symplify\ChangelogLinker\ChangelogLinker;
use Symplify\ChangelogLinker\ChangeTree\ChangeResolver;

final class ChangelogLinkerApplication
{
    /**
     * @var ChangeResolver
     */
    private $changeResolver;

    /**
     * @var ChangelogDumper
     */
    private $changelogDumper;

    /**
     * @var ChangelogLinker
     */
    private $changelogLinker;

    public function __construct(
        ChangeResolver $changeResolver,
        ChangelogDumper $changelogDumper,
        ChangelogLinker $changelogLinker
    ) {
        $this->changeResolver = $changeResolver;
        $this->changelogDumper = $changelogDumper;
        $this->changelogLinker = $changelogLinker;
    }

    public function createContentFromPullRequestsBySortPriority(array $pullRequests, string $changelogFormat): string
    {
        $changes = $this->changeResolver->resolveSortedChangesFromPullRequestsWithSortPriority(
            $pullRequests,
            $changelogFormat
        );

        $content = $this->changelogDumper->reportChangesWithHeadlines($changes, $changelogFormat);

        return $this->changelogLinker->processContent($content);
    }
}
