<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Analyzer\LinkedIdsAnalyzer;
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
     * @var LinkedIdsAnalyzer
     */
    private $linkedIdsAnalyzer;

    /**
     * @var LinkAppender
     */
    private $linkAppender;

    public function __construct(string $repositoryUrl, LinkedIdsAnalyzer $linkedIdsAnalyzer, LinkAppender $linkAppender)
    {
        $this->repositoryUrl = $repositoryUrl;
        $this->linkedIdsAnalyzer = $linkedIdsAnalyzer;
        $this->linkAppender = $linkAppender;
    }

    public function processContent(string $content): string
    {
        $this->processPullRequestAndIssueReferences($content);
        $this->processCommitReferences($content);

        return $content;
    }

    public function getPriority(): int
    {
        return 700;
    }

    private function processPullRequestAndIssueReferences(string $content): void
    {
        $matches = Strings::matchAll($content, '#\[' . RegexPattern::PR_OR_ISSUE . '\][\s,]#');
        foreach ($matches as $match) {
            if ($this->shouldSkipPullRequestOrIssueReference($match)) {
                continue;
            }

            $markdownLink = sprintf('[#%d]: %s/pull/%d', $match['id'], $this->repositoryUrl, $match['id']);
            $this->linkAppender->add($match['id'], $markdownLink);
        }
    }

    private function processCommitReferences(string $content): void
    {
        $matches = Strings::matchAll($content, '# \[' . RegexPattern::COMMIT . '\] #');
        foreach ($matches as $match) {
            $markdownLink = sprintf('[%s]: %s/commit/%s', $match['commit'], $this->repositoryUrl, $match['commit']);
            $this->linkAppender->add($match['commit'], $markdownLink);
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

        return $this->linkedIdsAnalyzer->hasLinkedId($match['id']);
    }
}
