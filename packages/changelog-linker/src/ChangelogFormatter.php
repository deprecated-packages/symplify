<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker;

use Nette\Utils\Strings;

/**
 * @see \Symplify\ChangelogLinker\Tests\ChangelogFormatter\ChangelogFormatterTest
 */
final class ChangelogFormatter
{
    /**
     * @see https://regex101.com/r/JmKFH1/1
     * @var string
     */
    private const HEADLINE_PATTERN = '#^(?<headline>[\#]{2,} [\w\d.\-/ ]+)$#m';

    /**
     * @var string
     */
    private const TWO_LINES_START_PATTERN = '#^(\n){2,}#';

    /**
     * @var string
     */
    private const THREE_LINES_PATTERN = '#(\n){3,}#';

    public function format(string $content): string
    {
        $content = $this->wrapHeadlinesWithEmptyLines($content);

        $content = $this->removeSuperfluousSpaces($content);

        return ltrim($content);
    }

    /**
     * Before:
     * # Headline\n
     *
     * After:
     * \n
     * # Headline\n
     * \n
     */
    private function wrapHeadlinesWithEmptyLines(string $content): string
    {
        return Strings::replace($content, self::HEADLINE_PATTERN, function (array $match): string {
            return PHP_EOL . $match['headline'] . PHP_EOL;
        });
    }

    private function removeSuperfluousSpaces(string $content): string
    {
        // 2 lines from the start
        $content = Strings::replace($content, self::TWO_LINES_START_PATTERN, PHP_EOL);

        // 3 lines to 2
        return Strings::replace($content, self::THREE_LINES_PATTERN, PHP_EOL . PHP_EOL);
    }
}
