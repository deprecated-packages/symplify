<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Analyzer;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Regex\RegexPattern;

final class IdsAnalyzer
{
    public function getLastIdInChangelog(string $filePath): int
    {
        $changelogContent = file_get_contents($filePath);

        $match = Strings::match($changelogContent, '#' . RegexPattern::PR_OR_ISSUE . '#');
        if ($match) {
            return (int) $match['id'];
        }

        return 1;
    }
}
