<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
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
     * @var LinkAppender
     */
    private $linkAppender;

    public function __construct(string $repositoryUrl, LinkAppender $linkAppender)
    {
        $this->linkAppender = $linkAppender;
        $this->repositoryUrl = $repositoryUrl;
    }

    /**
     * Github can redirects PRs to issues, so no need to trouble with their separatoin
     * @inspiration for Regex: https://stackoverflow.com/a/406408/1348344
     */
    public function processContent(string $content): string
    {
        $matches = Strings::matchAll($content, '#\[' . RegexPattern::PR_OR_ISSUE . '\]#m');
        foreach ($matches as $match) {
            $link = sprintf('[#%d]: %s/pull/%d', $match['id'], $this->repositoryUrl, $match['id']);
            $this->linkAppender->add($match['id'], $link);
        }

        return $content;
    }

    public function getPriority(): int
    {
        return 700;
    }
}
