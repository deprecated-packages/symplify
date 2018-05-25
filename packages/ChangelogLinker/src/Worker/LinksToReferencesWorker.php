<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Analyzer\LinksAnalyzer;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\LinkAppender;
use Symplify\ChangelogLinker\Regex\RegexPattern;

final class LinksToReferencesWorker implements WorkerInterface
{
    /**
     * @var string
     */
    private $repositoryUrl;

    /**
     * @var LinksAnalyzer
     */
    private $linksAnalyzer;

    /**
     * @var LinkAppender
     */
    private $linkAppender;

    public function __construct(string $repositoryUrl, LinksAnalyzer $linksAnalyzer, LinkAppender $linkAppender)
    {
        $this->repositoryUrl = $repositoryUrl;
        $this->linksAnalyzer = $linksAnalyzer;
        $this->linkAppender = $linkAppender;
    }

    public function processContent(string $content): string
    {
        // order matters, issues first
        $this->processIssues($content);
        $this->processPullRequests($content);
        $this->processCommitReferences($content);

        return $content;
    }

    public function getPriority(): int
    {
        return 700;
    }

    private function processIssues(string $content): void
    {
        $matches = Strings::matchAll($content, '#fixes \[' . RegexPattern::PR_OR_ISSUE . '\]#');

        foreach ($matches as $match) {
            if ($this->shouldSkipPullRequestOrIssueReference($match)) {
                continue;
            }

            $link = sprintf('[#%d]: %s/issues/%d', $match['id'], $this->repositoryUrl, $match['id']);
            $this->linkAppender->add($match['id'], $link);
        }
    }

    private function processPullRequests(string $content): void
    {
        $matches = Strings::matchAll($content, '#\[' . RegexPattern::PR_OR_ISSUE . '\]#');

        foreach ($matches as $match) {
            if ($this->shouldSkipPullRequestOrIssueReference($match)) {
                continue;
            }

            $link = sprintf('[#%d]: %s/pull/%d', $match['id'], $this->repositoryUrl, $match['id']);
            $this->linkAppender->add($match['id'], $link);
        }
    }

    private function processCommitReferences(string $content): void
    {
        $matches = Strings::matchAll($content, '# \[' . RegexPattern::COMMIT . '\] #');
        foreach ($matches as $match) {
            $link = sprintf('[%s]: %s/commit/%s', $match['commit'], $this->repositoryUrl, $match['commit']);
            $this->linkAppender->add($match['commit'], $link);
        }
    }

    /**
     * @param string[] $match
     */
    private function shouldSkipPullRequestOrIssueReference(array $match): bool
    {
        if ($this->linkAppender->hasId($match['id'])) {
            return true;
        }

        return $this->linksAnalyzer->hasLinkedId($match['id']);
    }
}
