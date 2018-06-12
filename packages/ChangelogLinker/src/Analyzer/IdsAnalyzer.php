<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Analyzer;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Regex\RegexPattern;

final class IdsAnalyzer
{
    public function getHighestIdInChangelog(string $filePath): int
    {
        $changelogContent = file_get_contents($filePath);

        $matches = Strings::matchAll($changelogContent, '#- \[?(\#(?<id>[0-9]+))\]?#');
        if (! $matches) {
            return 1;
        }

        $ids = array_column($matches, 'id');

        return (int) max(($ids));
    }
}
