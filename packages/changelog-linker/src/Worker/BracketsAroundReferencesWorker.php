<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\ValueObject\RegexPattern;

/**
 * Comletes [] around commit, pull-request, issues and version references
 */
final class BracketsAroundReferencesWorker implements WorkerInterface
{
    /**
     * @var string[]
     * @see https://help.github.com/articles/closing-issues-using-keywords/
     */
    private const CLOSES_KEYWORDS = [
        'close',
        'closes',
        'closed',
        'fix',
        'fixes',
        'fixed',
        'resolve',
        'resolves',
        'resolved',
    ];

    public function processContent(string $content): string
    {
        // issue or PR references
        $content = Strings::replace(
            $content,
            '#' . RegexPattern::PR_OR_ISSUE_NOT_IN_BRACKETS . '#',
            function (array $match): string {
                if (isset($match['reference'])) {
                    return sprintf('[%s]', $match['reference']);
                }

                // skip "[#321]" and "[see PHP bug #321]"
                return $match[0];
            }
        );

        // version references
        $content = Strings::replace($content, '#\#\# ' . RegexPattern::VERSION_REGEX . '#', '## [$1]');

        $content = $this->wrapClosesKeywordIds($content);

        // user references
        return Strings::replace($content, '# ' . RegexPattern::USER_REGEX . '#', ' [$1]');
    }

    public function getPriority(): int
    {
        return 1000;
    }

    private function wrapClosesKeywordIds(string $content): string
    {
        return Strings::replace(
            $content,
            sprintf('#(%s) \#%s#', implode('|', self::CLOSES_KEYWORDS), RegexPattern::VERSION_REGEX),
            '$1 [#$2]'
        );
    }
}
