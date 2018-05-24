<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Analyzer;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Regex\RegexPattern;

final class LinksAnalyzer
{
    /**
     * @var string[]
     */
    private $linkedIds = [];

    public function analyzeContent(string $content): void
    {
        $this->linkedIds = [];

        $pattern = sprintf('#\[%s|%s\]:\s+#', RegexPattern::PR_OR_ISSUE, RegexPattern::VERSION);
        $matches = Strings::matchAll($content, $pattern);

        foreach ($matches as $match) {
            $this->linkedIds[] = $match['id'] ?? $match['version'];
        }
    }

    public function hasLinkedId(string $id): bool
    {
        return in_array($id, $this->linkedIds, true);
    }
}
