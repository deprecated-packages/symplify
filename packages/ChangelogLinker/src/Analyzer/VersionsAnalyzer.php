<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Analyzer;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Regex\RegexPattern;

final class VersionsAnalyzer
{
    /**
     * @var string[]
     */
    private $versions = [];

    public function analyzeContent(string $content): void
    {
        $this->versions = [];

        $matches = Strings::matchAll($content, '#\#\# (\[)?' . RegexPattern::VERSION . '(\])?#');
        foreach ($matches as $match) {
            $this->versions[] = $match['version'];
        }
    }

    public function hasLinkedVersion(string $version): bool
    {
        return in_array($version, $this->versions, true);
    }

    /**
     * @return string[]
     */
    public function getVersions(): array
    {
        return $this->versions;
    }

    public function isLastVersion(string $version): bool
    {
        return array_search($version, $this->versions, true) === 0;
    }
}
