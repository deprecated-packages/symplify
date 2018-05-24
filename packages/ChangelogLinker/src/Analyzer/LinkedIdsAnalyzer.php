<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Analyzer;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Regex\RegexPattern;

final class LinkedIdsAnalyzer
{
    /**
     * @var string[]
     */
    private $linkedIds = [];

    public function analyzeContent(string $content): void
    {
        $this->linkedIds = [];

        $matches = Strings::matchAll($content, '#\[' . RegexPattern::PR_OR_ISSUE . '\]:\s+#');
        foreach ($matches as $match) {
            $this->linkedIds[] = $match['id'];
        }
    }

    public function hasLinkedId(string $id): bool
    {
        return in_array($id, $this->linkedIds, true);
    }
}
