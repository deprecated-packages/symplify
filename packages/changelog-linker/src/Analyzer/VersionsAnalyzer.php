<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Analyzer;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\ValueObject\RegexPattern;

/**
 * @see \Symplify\ChangelogLinker\Tests\Analyzer\VersionsAnalyzer\VersionsAnalyzerTest
 */
final class VersionsAnalyzer
{
    /**
     * @var string[]
     */
    private $versions = [];

    public function analyzeContent(string $content): void
    {
        $this->versions = [];

        $matches = Strings::matchAll($content, '#\#\# (\[)?' . RegexPattern::VERSION_REGEX . '(\])?#');
        foreach ($matches as $match) {
            $this->versions[] = $match['version'];
        }
    }

    /**
     * @return string[]
     */
    public function getVersions(): array
    {
        return $this->versions;
    }
}
