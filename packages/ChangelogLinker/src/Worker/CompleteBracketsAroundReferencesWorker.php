<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;

/**
 * Comletes [] around commit, pull-request, issues and version references
 */
final class CompleteBracketsAroundReferencesWorker implements WorkerInterface
{
    public function processContent(string $content, string $repositoryLink): string
    {
        // issue or PR references
        $content = Strings::replace($content, '# (?<reference>\#(v|[0-9])[a-zA-Z0-9\.-]+) #', function (array $match): string {
            return sprintf(' [%s] ', $match['reference']);
        });

        // version references
        $content = Strings::replace($content, '#\#\# (?<versionId>(v|[0-9])[a-zA-Z0-9\.-]+)#', function (array $match): string {
            return sprintf('## [%s]', $match['versionId']);
        });

        return $content;
    }
}
