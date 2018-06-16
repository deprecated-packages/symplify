<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console\Formatter;

use Nette\Utils\Strings;

final class DumpMergesFormatter
{
    /**
     * @var string
     */
    private const HEADLINE_PATTERN = '#^(?<headline>[\#]{2,} [A-Za-z0-9.\- ]+)$#m';

    public function format(string $content): string
    {
        $content = $this->wrapHeadlinesWithEmptyLines($content);

        return $this->removeSuperfluousSpaces($content);

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
        return Strings::replace($content, self::HEADLINE_PATTERN, function (array $match) {
            return PHP_EOL . $match['headline'] . PHP_EOL;
        });
    }

    private function removeSuperfluousSpaces(string $content): string
    {
        // 2 lines from the start
        $content = Strings::replace($content, '#^(\n){2,}#', PHP_EOL);

        // 3 lines to 2
        return Strings::replace($content, '#(\n){3,}#', PHP_EOL . PHP_EOL);
    }
}
