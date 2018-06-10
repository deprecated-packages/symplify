<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Configuration\Configuration;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\LinkAppender;
use Symplify\ChangelogLinker\Regex\RegexPattern;

final class LinksToReferencesWorker implements WorkerInterface
{
    /**
     * @var LinkAppender
     */
    private $linkAppender;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration, LinkAppender $linkAppender)
    {
        $this->linkAppender = $linkAppender;
        $this->configuration = $configuration;
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
        $matches = Strings::matchAll($content, '#(fixes|resolves) \[' . RegexPattern::PR_OR_ISSUE . '\]#');

        foreach ($matches as $match) {
            $link = sprintf(
                '[#%d]: %s/issues/%d',
                $match['id'],
                $this->configuration->getRepositoryUrl(),
                $match['id']
            );
            $this->linkAppender->add($match['id'], $link);
        }
    }

    /**
     * @inspiration for Regex: https://stackoverflow.com/a/406408/1348344
     */
    private function processPullRequests(string $content): void
    {
        $matches = Strings::matchAll($content, '#^((?!fixes|resolves).)*\[' . RegexPattern::PR_OR_ISSUE . '\]#m');

        foreach ($matches as $match) {
            $link = sprintf('[#%d]: %s/pull/%d', $match['id'], $this->configuration->getRepositoryUrl(), $match['id']);
            $this->linkAppender->add($match['id'], $link);
        }
    }

    private function processCommitReferences(string $content): void
    {
        $matches = Strings::matchAll($content, '# \[' . RegexPattern::COMMIT . '\] #');
        foreach ($matches as $match) {
            $link = sprintf(
                '[%s]: %s/commit/%s',
                $match['commit'],
                $this->configuration->getRepositoryUrl(),
                $match['commit']
            );
            $this->linkAppender->add($match['commit'], $link);
        }
    }
}
