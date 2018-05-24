<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Analyzer;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Regex\RegexPattern;

final class LinkedVersionsAnalyzer
{
    /**
     * @var string[]
     */
    private $linkedVersions = [];

    public function analyzeContent(string $content): void
    {
        $this->linkedVersions = [];

        $matches = Strings::matchAll($content, '#\[' . RegexPattern::VERSION . '\]: #');
        foreach ($matches as $match) {
            $this->linkedVersions[] = $match['version'];
        }
    }

    public function hasLinkedVersion(string $version): bool
    {
        return in_array($version, $this->linkedVersions, true);
    }

    /**
     * @return string[]
     */
    public function getLinkedVersions(): array
    {
        return $this->linkedVersions;
    }
}
