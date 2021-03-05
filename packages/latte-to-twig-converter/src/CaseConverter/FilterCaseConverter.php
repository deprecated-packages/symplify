<?php

declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class FilterCaseConverter implements CaseConverterInterface
{
    /**
     * @see https://regex101.com/r/J1sXq9/1/
     * @var string
     */
    private const VALUE_WITH_FILTER_REGEX = '#(.*?)\|(.*?):(.*?)#ms';

    /**
     * @see https://regex101.com/r/04QMgW/1
     * @var string
     */
    private const VALUE_FILTER_REGEX = '#{(?<value>.*)\|(?<filter>[a-z]+):(?<args>(.*))[\n|\||}]#ms';

    /**
     * @var array<string, string>
     */
    private const FILTER_RENAMES = [
        // latte name → twig name
        'number' => 'number_format',
    ];

    /**
     * @see https://regex101.com/r/CHm8rF/1
     * @var string
     */
    private const COUNT_FUNCTION_REGEX = '#{(.*?) count\(\$?(\w+)\)(.*?)}#';

    /**
     * @see https://regex101.com/r/FiLM81/1
     * @var string
     */
    private const IN_BRACKET_REGEX = '#{(.*?)}#ms';

    /**
     * @see https://regex101.com/r/2Ltgxb/2
     * @var string
     */
    private const NOESCAPE_REGEX = '#\|(\s+)?noescape#';

    public function getPriority(): int
    {
        return 500;
    }

    public function convertContent(string $content): string
    {
        /**
         * | noescape → | raw
         */
        $content = Strings::replace($content, self::NOESCAPE_REGEX, '|$1raw');

        /**
         * {$value|date:'j. n. Y'} → {{ value|date('j. n. Y') }}
         *
         * {$11874|number:0:',':' '} → {{ 11874|number_format(0, ',', ' ') }}
         */
        $content = Strings::replace($content, self::IN_BRACKET_REGEX, function (array $match): string {
            // has some filter with args?
            if (! Strings::match($match[1], self::VALUE_WITH_FILTER_REGEX)) {
                return $match[0];
            }

            $match[0] = Strings::replace($match[0], self::VALUE_FILTER_REGEX, function (array $subMatch): string {
                // filter renames
                $filterName = self::FILTER_RENAMES[$subMatch['filter']] ?? $subMatch['filter'];
                $arguments = $this->replaceSeparator($subMatch['args']);

                $value = $subMatch['value'];
                $value = ltrim($value, '$');

                if ($filterName !== 'number_format') {
                    return sprintf('{{ %s|%s(%s) }}', $value, $filterName, $arguments);
                }

                if ($this->shouldWrapNumber($value)) {
                    return sprintf('{{ (%s)|%s(%s) }}', $value, $filterName, $arguments);
                }

                return sprintf('{{ %s|%s(%s) }}', $value, $filterName, $arguments);
            });

            return $match[0];
        });

        /**
         * ... count(5) ↓ ... 5|length
         */
        return Strings::replace($content, self::COUNT_FUNCTION_REGEX, '{$1 $2|length$3}');
    }

    private function replaceSeparator(string $arguments): string
    {
        return Strings::replace($arguments, '#:#', ', ');
    }

    private function shouldWrapNumber(string $value): bool
    {
        return ! is_numeric($value);
    }
}
