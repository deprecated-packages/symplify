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

        foreach (Strings::matchAll($content, RegexPattern::LINK_REFERENCE) as $match) {
            $this->linkedIds[] = $match['reference'];
        }
    }

    public function hasLinkedId(string $id): bool
    {
        return in_array($id, $this->linkedIds, true);
    }
}
