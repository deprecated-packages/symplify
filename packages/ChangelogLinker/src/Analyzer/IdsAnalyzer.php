<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Analyzer;

use Nette\Utils\Strings;

final class IdsAnalyzer
{
    /**
     * @var string
     *
     * Covers cases like:
     * - #5 Add this => 5
     * - [#10] Change that => 10
     */
    private const PR_REFERENCE_IN_LIST = '#- \[?(\#(?<id>\d+))\]?#';

    public function getHighestIdInChangelog(string $content): int
    {
        $matches = Strings::matchAll($content, self::PR_REFERENCE_IN_LIST);
        if (! $matches) {
            return 0;
        }

        $ids = array_column($matches, 'id');

        return (int) max($ids);
    }
}
