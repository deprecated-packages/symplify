<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class FilterCaseConverter implements CaseConverterInterface
{
    /**
     * @var string[]
     */
    private $filterRenames = [
        // latte name → twig name
        'number' => 'number_format',
    ];

    public function getPriority(): int
    {
        return 500;
    }

    public function convertContent(string $content): string
    {
        // | noescape =>
        // | raw
        $content = Strings::replace($content, '#\|(\s+)?noescape#', '|$1raw');

        // {$value|date:'j. n. Y'} =>
        // {{ value|date('j. n. Y') }}
        // {$11874|number:0:',':' '}
        // {{ 11874|number_format(0, ',', ' ') }}
        $content = Strings::replace($content, '#{(.*?)}#ms', function (array $match) {
            // has some filter with args?
            if (! Strings::match($match[1], '#(.*?)\|(.*?):(.*?)#ms')) {
                return $match[0];
            }

            // https://regex101.com/r/04QMgW/1
            $match[0] = Strings::replace($match[0], '#{(?<value>.*)\|(?<filter>[a-z]+):(?<args>(.*))[\n|\||}]#ms', function (
                array $subMatch
            ) {
                // filter renames
                $filterName = $this->filterRenames[$subMatch['filter']] ?? $subMatch['filter'];
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

        // ... count(5) =>
        // ... 5|length
        return Strings::replace($content, '#{(.*?) count\(\$?(\w+)\)(.*?)}#', '{$1 $2|length$3}');
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
