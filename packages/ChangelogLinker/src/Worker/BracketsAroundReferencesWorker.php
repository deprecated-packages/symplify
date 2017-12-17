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
    public function processContent(string $content, string $repositoryLink): string
    {
        // issue or PR references
        $content = Strings::replace($content, '# ' . RegexPattern::PR_OR_ISSUE . '#', function (array $match): string {
            return sprintf(' [%s]', $match['reference']);
        });

        // version references
        $content = Strings::replace($content, '#\#\# ' . RegexPattern::VERSION . '#', function (array $match): string {
            return sprintf('## [%s]', $match['version']);
        });

        // commit references
        $content = Strings::replace($content, '# ' . RegexPattern::COMMIT . '#', function (array $match): string {
            return sprintf(' [%s]', $match['commit']);
        });

        return $content;
    }
}
