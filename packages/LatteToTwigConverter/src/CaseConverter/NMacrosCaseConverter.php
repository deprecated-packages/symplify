<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

/**
 * This needs to be run first, since it only move n:sytax to {syntax}...{/syntax} - all in Latte
 * Other case converters will change it then to Twig.
 *
 * @see https://regex101.com/r/sOgdcK/1
 */
final class NMacrosCaseConverter implements CaseConverterInterface
{
    public function getPriority(): int
    {
        return 1500;
    }

    public function convertContent(string $content): string
    {
        // n:if
        $content = Strings::replace(
            $content,
            $this->createPattern('if'),
            function (array $match) {
                return sprintf(
                    '{if %s}%s%s%s%s%s%s{/if}',
                    $match['expression'],
                    PHP_EOL,
                    $match['openTagStart'],
                    $match['openTagEnd'],
                    $match['inner'],
                    $match['closeTag'],
                    PHP_EOL
                );
            }
        );

        // n:ifset
        $content = Strings::replace(
            $content,
            $this->createPattern('ifset'),
            function (array $match) {
                return sprintf(
                    '{ifset %s}%s%s%s%s%s%s{/ifset}',
                    $match['expression'],
                    PHP_EOL,
                    $match['openTagStart'],
                    $match['openTagEnd'],
                    $match['inner'],
                    $match['closeTag'],
                    PHP_EOL
                );
            }
        );

        // n:foreach
        $content = Strings::replace(
            $content,
            $this->createPattern('foreach'),
            function (array $match) {
                return sprintf(
                    '{foreach %s}%s%s%s%s%s%s{/foreach}',
                    $match['expression'],
                    PHP_EOL,
                    $match['openTagStart'],
                    $match['openTagEnd'],
                    $match['inner'],
                    $match['closeTag'],
                    PHP_EOL
                );
            }
        );

        // n:inner-foreach
        $content = Strings::replace(
            $content,
            $this->createPattern('inner-foreach'),
            function (array $match) {
                return sprintf(
                    '%s%s%s{foreach %s}%s{/foreach}%s%s',
                    $match['openTagStart'],
                    $match['openTagEnd'],
                    PHP_EOL,
                    $match['expression'],
                    $match['inner'],
                    PHP_EOL,
                    $match['closeTag']
                );
            }
        );

        return $content;
    }

    private function createPattern(string $macro): string
    {
        return '#(?<openTagStart><(?<tag>\w+)[^<]*?) n:' . $macro . '="(?<expression>.*?)"(?<openTagEnd>.*?>)(?<inner>.*?)(?<closeTag><\/\2>)#sm';
    }
}
