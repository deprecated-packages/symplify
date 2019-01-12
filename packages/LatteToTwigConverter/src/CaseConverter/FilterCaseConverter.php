<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;
use function Safe\sprintf;

final class FilterCaseConverter implements CaseConverterInterface
{
    /**
     * @var string[]
     */
    private $filterRenames = [
        // latte name → twig name
        'number' => 'number_format',
    ];

    public function convertContent(string $content): string
    {
        // {$post['updated_message']|noescape} =>
        // {{ post.updated_message|noescape }}
        $content = Strings::replace($content, '#{\$([\w-]+)\[\'([\w-]+)\'\]\|([^}]+)}#', '{{ $1.$2|$3 }}');

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
                return '{' . $match[1] . '}';
            }

            // https://regex101.com/r/04QMgW/1
            $match[1] = Strings::replace($match[1], '#\|(?<filter>[a-z]+):(?<args>(.*))[\n|\|]?#ms', function (
                array $subMatch
            ) {
                // filter renames
                $filterName = $this->filterRenames[$subMatch['filter']] ?? $subMatch['filter'];
                $arguments = $this->replaceSeparator($subMatch['args']);

                return sprintf('|%s(%s)', $filterName, $arguments);
            });

            return '{' . $match[1] . '}';
        });

        // ... count(5) =>
        // ... 5|length
        return Strings::replace($content, '#{(.*?) count\(\$?(\w+)\)(.*?)}#', '{$1 $2|length$3}');
    }

    private function replaceSeparator(string $arguments): string
    {
        return Strings::replace($arguments, '#:#', ', ');
    }
}
