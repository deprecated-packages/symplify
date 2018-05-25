<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\Regex\RegexPattern;

/**
 * Comletes [] around commit, pull-request, issues and version references
 */
final class BracketsAroundReferencesWorker implements WorkerInterface
{
    public function processContent(string $content): string
    {
        // issue or PR references
        $content = Strings::replace(
            $content,
            '#' . RegexPattern::PR_OR_ISSUE_NOT_IN_BRACKETS . '#',
            function (array $match) {
                if (isset($match['reference'])) {
                    return sprintf('[%s]', $match['reference']);
                }

                // skip "[#321]" and "[see PHP bug #321]"
                return $match[0];
            }
        );

        // version references
        $content = Strings::replace($content, '#\#\# ' . RegexPattern::VERSION . '#', '## [$1]');

        // commit references
        $content = Strings::replace($content, '# ' . RegexPattern::COMMIT . '#', ' [$1]');

        // user references
        $content = Strings::replace($content, '# ' . RegexPattern::USER . '#', ' [$1]');

        return $content;
    }

    public function getPriority(): int
    {
        return 1000;
    }
}
