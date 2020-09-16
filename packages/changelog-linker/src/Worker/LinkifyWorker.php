<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\LinkAppender;

final class LinkifyWorker implements WorkerInterface
{
    /**
     * @var string
     */
    public const SPACE_START_REGEX = '#^\s+$#';

    /**
     * @var string
     */
    public const LINKS_REGEX = '#^\-(\s+)?\[\#\d+#';

    /**
     * @var array<string, string>
     */
    private $namesToUrls = [];

    /**
     * @var LinkAppender
     */
    private $linkAppender;

    /**
     * @param array<string, string> $namesToUrls
     */
    public function __construct(LinkAppender $linkAppender, array $namesToUrls)
    {
        $this->linkAppender = $linkAppender;
        $this->namesToUrls = $namesToUrls;
    }

    public function processContent(string $content): string
    {
        $contentLines = explode(PHP_EOL, $content);

        foreach ($contentLines as $key => $contentLine) {
            if ($this->shouldSkipContentLine($contentLine)) {
                continue;
            }

            $contentLines[$key] = $this->linkifyContentLine($contentLine);
        }

        return implode(PHP_EOL, $contentLines);
    }

    public function getPriority(): int
    {
        return 900;
    }

    private function shouldSkipContentLine(string $contentLine): bool
    {
        // skip spaces only
        if (Strings::match($contentLine, self::SPACE_START_REGEX)) {
            return true;
        }

        // skip links
        return (bool) Strings::match($contentLine, self::LINKS_REGEX);
    }

    private function linkifyContentLine(string $contentLine): string
    {
        foreach ($this->namesToUrls as $name => $url) {
            $quotedName = preg_quote($name, '#');

            if ($this->shouldSkipContentLineAndName($quotedName, $contentLine)) {
                continue;
            }

            $unlinkedPattern = '#\b(' . $quotedName . ')\b#';
            if (! Strings::match($contentLine, $unlinkedPattern)) {
                continue;
            }

            $contentLine = Strings::replace($contentLine, $unlinkedPattern, '[$1]');
            $link = sprintf('[%s]: %s', $name, $url);

            $this->linkAppender->add($name, $link);
        }

        return $contentLine;
    }

    private function shouldSkipContentLineAndName(string $quotedName, string $contentLine): bool
    {
        // is already linked
        $linkedPattern = '#\[' . $quotedName . '\]#';
        if (Strings::match($contentLine, $linkedPattern)) {
            return true;
        }

        // part of another string, e.g. "linked-", "to-be-linked"
        $partOfAnotherStringPattern = '#\-' . $quotedName . '|' . $quotedName . '\-#';

        return (bool) Strings::match($contentLine, $partOfAnotherStringPattern);
    }
}
