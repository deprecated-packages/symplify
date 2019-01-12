<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;
use function Safe\sprintf;

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
        $content = Strings::replace(
            $content,
            '#(?<openTagStart><.*?) n:if="(?<condition>.*?)"(?<openTagEnd>.*?>)(?<inner>.*?)(?<closeTag><\/(.*?)>)#sm',
            function (array $match) {
                return sprintf(
                    '{if %s}%s%s%s%s%s%s{/if}',
                    $match['condition'],
                    PHP_EOL,
                    $match['openTagStart'],
                    $match['openTagEnd'],
                    $match['inner'],
                    $match['closeTag'],
                    PHP_EOL
                );
            }
        );

        return $content;
    }
}
