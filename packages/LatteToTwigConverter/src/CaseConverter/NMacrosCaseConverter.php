<?php

declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;
use Symplify\PackageBuilder\Configuration\EolConfiguration;

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
            function (array $match): string {
                $eolChar = EolConfiguration::getEolChar();
                return sprintf(
                    '{if %s}%s%s%s%s%s%s{/if}',
                    $match['expression'],
                    $eolChar,
                    $match['openTagStart'],
                    $match['openTagEnd'],
                    $match['inner'],
                    $match['closeTag'],
                    $eolChar
                );
            }
        );

        // n:ifset
        $content = Strings::replace(
            $content,
            $this->createPattern('ifset'),
            function (array $match): string {
                $eolChar = EolConfiguration::getEolChar();
                return sprintf(
                    '{ifset %s}%s%s%s%s%s%s{/ifset}',
                    $match['expression'],
                    $eolChar,
                    $match['openTagStart'],
                    $match['openTagEnd'],
                    $match['inner'],
                    $match['closeTag'],
                    $eolChar
                );
            }
        );

        // n:foreach
        $content = Strings::replace(
            $content,
            $this->createPattern('foreach'),
            function (array $match): string {
                $eolChar = EolConfiguration::getEolChar();
                return sprintf(
                    '{foreach %s}%s%s%s%s%s%s{/foreach}',
                    $match['expression'],
                    $eolChar,
                    $match['openTagStart'],
                    $match['openTagEnd'],
                    $match['inner'],
                    $match['closeTag'],
                    $eolChar
                );
            }
        );

        // n:inner-foreach
        $content = Strings::replace(
            $content,
            $this->createPattern('inner-foreach'),
            function (array $match): string {
                $eolChar = EolConfiguration::getEolChar();
                return sprintf(
                    '%s%s%s{foreach %s}%s{/foreach}%s%s',
                    $match['openTagStart'],
                    $match['openTagEnd'],
                    $eolChar,
                    $match['expression'],
                    $match['inner'],
                    $eolChar,
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
