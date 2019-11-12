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

    public function getHighestIdInChangelog(string $content): ?int
    {
        $ids = $this->getAllIdsInChangelog($content);
        return (int) max($ids);
    }

    public function getAllIdsInChangelog(string $content): ?array
    {
        $matches = Strings::matchAll($content, self::PR_REFERENCE_IN_LIST);
        if ($matches === []) {
            return null;
        }
        return array_column($matches, 'id');
    }
}
