<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Analyzer\LinkedIdsAnalyzer;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\Regex\RegexPattern;

final class LinksToReferencesWorker implements WorkerInterface
{
    /**
     * @var string
     */
    private $repositoryUrl;

    /**
     * @var LinkedIdsAnalyzer
     */
    private $linkedIdsAnalyzer;

    public function __construct(string $repositoryUrl, LinkedIdsAnalyzer $linkedIdsAnalyzer)
    {
        $this->repositoryUrl = $repositoryUrl;
        $this->linkedIdsAnalyzer = $linkedIdsAnalyzer;
    }

    public function processContent(string $content): string
    {
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

    /**
     * @param string[] $match
     * @param string[] $linksToAppend
     */
    private function shouldSkipPullRequestOrIssueReference(array $match, array $linksToAppend): bool
    {
        if (array_key_exists($match['id'], $linksToAppend)) {
            return true;
        }

        return $this->linkedIdsAnalyzer->hasLinkedId($match['id']);
    }
}
