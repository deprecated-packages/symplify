<?php

declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

/**
 * This needs to be run first, since it only move n:sytax to {syntax}...{/syntax} - all in Latte Other case converters
 * will change it then to Twig.
 *
 * @see https://regex101.com/r/sOgdcK/1
 */
final class NMacrosCaseConverter implements CaseConverterInterface
{
    /**
     * @var string
     */
    private const EXPRESSION = 'expression';

    /**
     * @var string
     */
    private const OPEN_TAG_START = 'open_tag_start';

    /**
     * @var string
     */
    private const OPEN_TAG_END = 'open_tag_end';

    /**
     * @var string
     */
    private const INNER = 'inner';

    /**
     * @var string
     */
    private const CLOSE_TAG = 'close_tag';

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
            fn (array $match): string => sprintf(
                '{if %s}%s%s%s%s%s%s{/if}',
                $match[self::EXPRESSION],
                PHP_EOL,
                $match[self::OPEN_TAG_START],
                $match[self::OPEN_TAG_END],
                $match[self::INNER],
                $match[self::CLOSE_TAG],
                PHP_EOL
            )
        );

        // n:ifset
        $content = Strings::replace(
            $content,
            $this->createPattern('ifset'),
            fn (array $match): string => sprintf(
                '{ifset %s}%s%s%s%s%s%s{/ifset}',
                $match[self::EXPRESSION],
                PHP_EOL,
                $match[self::OPEN_TAG_START],
                $match[self::OPEN_TAG_END],
                $match[self::INNER],
                $match[self::CLOSE_TAG],
                PHP_EOL
            )
        );

        // n:foreach
        $content = Strings::replace(
            $content,
            $this->createPattern('foreach'),
            fn (array $match): string => sprintf(
                '{foreach %s}%s%s%s%s%s%s{/foreach}',
                $match[self::EXPRESSION],
                PHP_EOL,
                $match[self::OPEN_TAG_START],
                $match[self::OPEN_TAG_END],
                $match[self::INNER],
                $match[self::CLOSE_TAG],
                PHP_EOL
            )
        );

        // n:inner-foreach
        return Strings::replace(
            $content,
            $this->createPattern('inner-foreach'),
            fn (array $match): string => sprintf(
                '%s%s%s{foreach %s}%s{/foreach}%s%s',
                $match[self::OPEN_TAG_START],
                $match[self::OPEN_TAG_END],
                PHP_EOL,
                $match[self::EXPRESSION],
                $match[self::INNER],
                PHP_EOL,
                $match[self::CLOSE_TAG]
            )
        );
    }

    private function createPattern(string $macro): string
    {
        return '#(?<' . self::OPEN_TAG_START . '><(?<tag>\w+)[^<]*?) n:' . $macro . '="(?<expression>.*?)"(?<' . self::OPEN_TAG_END . '>.*?>)(?<inner>.*?)(?<' . self::CLOSE_TAG . '><\/\2>)#sm';
    }
}
