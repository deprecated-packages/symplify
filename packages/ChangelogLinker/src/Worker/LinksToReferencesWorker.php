<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\Regex\RegexPattern;

final class LinksToReferencesWorker implements WorkerInterface
{
    /**
     * @var string[]
     */
    private $linkedIds = [];

    /**
     * @var string
     */
    private $repositoryUrl;

    public function __construct(string $repositoryUrl)
    {
        $this->repositoryUrl = $repositoryUrl;
    }

    public function processContent(string $content): string
    {
        $this->resolveLinkedElements($content);

        $linksToAppend = $this->processPullRequestAndIssueReferences($content);
        $linksToAppend = array_merge($linksToAppend, $this->processCommitReferences($content));

        if (! count($linksToAppend)) {
            return $content;
        }

        rsort($linksToAppend);

        // append new links to the file
        return $content . PHP_EOL . implode(PHP_EOL, $linksToAppend);
    }

    public function getPriority(): int
    {
        return 700;
    }

    /**
     * @return string[]
     */
    private function processPullRequestAndIssueReferences(string $content): array
    {
        $linksToAppend = [];

        $matches = Strings::matchAll($content, '#\[' . RegexPattern::PR_OR_ISSUE . '\][\s,]#');
        foreach ($matches as $match) {
            if ($this->shouldSkipPullRequestOrIssueReference($match, $linksToAppend)) {
                continue;
            }

            $markdownLink = sprintf('[#%d]: %s/pull/%d', $match['id'], $this->repositoryUrl, $match['id']);
            $linksToAppend[$match['id']] = $markdownLink;
        }

        return $linksToAppend;
    }

    /**
     * @return string[]
     */
    private function processCommitReferences(string $content): array
    {
        $linksToAppend = [];

        $matches = Strings::matchAll($content, '# \[' . RegexPattern::COMMIT . '\] #');
        foreach ($matches as $match) {
            $markdownLink = sprintf('[%s]: %s/commit/%s', $match['commit'], $this->repositoryUrl, $match['commit']);

            $linksToAppend[$match['commit']] = $markdownLink;
        }

        return $linksToAppend;
    }

    private function resolveLinkedElements(string $content): void
    {
        $matches = Strings::matchAll($content, '#\[' . RegexPattern::PR_OR_ISSUE . '\]:\s+#');
        foreach ($matches as $match) {
            $this->linkedIds[] = $match['id'];
        }
    }

    /**
     * @param string[] $match
     * @param string[] $linksToAppend
     */
    private function shouldSkipPullRequestOrIssueReference(array $match, array $linksToAppend): bool
    {
        if (array_key_exists($match['id'], $linksToAppend)) {
            return true;
        }

        if (in_array($match['id'], $this->linkedIds, true)) {
            return true;
        }

        return false;
    }
}
