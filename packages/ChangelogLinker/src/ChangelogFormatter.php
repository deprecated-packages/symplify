<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker;

use Nette\Utils\Strings;
use Symplify\PackageBuilder\Configuration\EolConfiguration;

final class ChangelogFormatter
{
    /**
     * @see https://regex101.com/r/JmKFH1/1
     * @var string
     */
    private const HEADLINE_PATTERN = '#^(?<headline>[\#]{2,} [\w\d.\-/ ]+)$#m';

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
        return Strings::replace($content, self::HEADLINE_PATTERN, function (array $match): string {
            $eolChar = EolConfiguration::getEolChar();
            return $eolChar . $match['headline'] . $eolChar;
        });
    }

    private function removeSuperfluousSpaces(string $content): string
    {
        $eolChar = EolConfiguration::getEolChar();

        // 2 lines from the start
        $content = Strings::replace($content, '#^(\n){2,}#', $eolChar);

        // 3 lines to 2
        return Strings::replace($content, '#(\n){3,}#', $eolChar . $eolChar);
    }
}
