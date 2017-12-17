<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;

/**
 * Comletes [] around commit, pull-request, issues and version references
 */
final class BracketsAroundReferencesWorker implements WorkerInterface
{
    /**
     * @var string
     */
    private const ISSUE_OR_PR_ID_PATTERN = '# (?<reference>\#(v|[0-9])[a-zA-Z0-9\.-]+) #';

    /**
     * @var string
     */
    private const VERSION_REFERENCE = '#\#\# (?<versionId>(v|[0-9])[a-zA-Z0-9\.-]+)#';

    /**
     * @var string
     */
    private const COMMIT_REFERENCE = '# (?<commit>[0-9a-z]{40}) #';

    public function processContent(string $content, string $repositoryLink): string
    {
        // issue or PR references
        $content = Strings::replace($content, self::ISSUE_OR_PR_ID_PATTERN, function (array $match): string {
            return sprintf(' [%s] ', $match['reference']);
        });

        // version references
        $content = Strings::replace($content, self::VERSION_REFERENCE, function (array $match): string {
            return sprintf('## [%s]', $match['versionId']);
        });

        // commit references
        $content = Strings::replace($content, self::COMMIT_REFERENCE, function (array $match): string {
            return sprintf(' [%s] ', $match['commit']);
        });

        return $content;
    }
}
