<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Analyzer;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Regex\RegexPattern;

/**
 * @see \Symplify\ChangelogLinker\Tests\Analyzer\LinksAnalyzer\LinksAnalyzerTest
 */
final class LinksAnalyzer
{
    /**
     * @var string
     * @see https://regex101.com/r/8L3ZvQ/1
     */
    private const REFERENCE_REGEX = '#\[\#?(?<reference>[(-\/@\w\d\.]+)\](?!:)(?!\()#';

    /**
     * @var string[]
     */
    private $linkedIds = [];

    /**
     * @var string[]
     */
    private $references = [];

    public function analyzeContent(string $content): void
    {
        // [content]: url
        $this->linkedIds = [];
        $matches = Strings::matchAll($content, RegexPattern::LINK_REFERENCE_REGEX);
        foreach ($matches as $match) {
            $this->linkedIds[] = $match['reference'];
        }
        $this->linkedIds = array_unique($this->linkedIds);

        // [content]
        $this->references = [];
        $matches = Strings::matchAll($content, self::REFERENCE_REGEX);
        foreach ($matches as $match) {
            $this->references[] = $match['reference'];
        }
        $this->references = array_unique($this->references);
    }

    public function hasLinkedId(string $id): bool
    {
        return in_array($id, $this->linkedIds, true);
    }

    /**
     * @return string[]
     */
    public function getDeadLinks(): array
    {
        $deadLinks = array_diff($this->linkedIds, $this->references);

        // special link, that needs to be kept
        $commentPosition = array_search('comment', $deadLinks, true);
        if ($commentPosition !== false) {
            unset($deadLinks[$commentPosition]);
        }

        return array_values($deadLinks);
    }
}
