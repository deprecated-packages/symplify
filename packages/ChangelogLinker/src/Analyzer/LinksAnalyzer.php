<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Analyzer;

use Nette\Utils\Strings;

final class LinksAnalyzer
{
    /**
     * @var string[]
     */
    private $linkedIds = [];

    public function analyzeContent(string $content): void
    {
        $this->linkedIds = [];

        // matches any links: "[<...>]: http://"
        $matches = Strings::matchAll($content, '#\[\#?(?<reference>.*)\]:\s+#');
        foreach ($matches as $match) {
            $this->linkedIds[] = $match['reference'];
        }
    }

    public function hasLinkedId(string $id): bool
    {
        return in_array($id, $this->linkedIds, true);
    }
}
