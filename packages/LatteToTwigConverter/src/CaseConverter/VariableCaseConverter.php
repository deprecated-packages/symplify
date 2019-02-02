<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class VariableCaseConverter implements CaseConverterInterface
{
    /**
     * @var string
     *
     * Matches:
     * ->someMethodCall()
     */
    private const PATTERN_METHOD_CALL = '->([\w\-\(\)]+)';

    /**
     * @var string
     *
     * Matches:
     * ['someValue']
     */
    private const PATTERN_ARRAY_ACCESS = '\[\'([\w\-]+)\'\]';

    public function getPriority(): int
    {
        return 200;
    }

    public function convertContent(string $content): string
    {
        // quote in-script variables, they're auto-quoted by Latte
        $content = Strings::replace($content, '#<script(.*?)>(.*?)</script>#s', function (array $match) {
            $match[2] = Strings::replace($match[2], '#({\$(.*?)})#', '\'$1\'');

            return sprintf('<script%s>%s</script>', $match[1], $match[2]);
        });

        // {$post->getId()} =>
        // {{ post.getId() }}
        $content = Strings::replace($content, '#{\$([\w-]+)' . self::PATTERN_METHOD_CALL . '(.*?)}#', '{{ $1.$2$3 }}');

        // {$post['relativeUrl']} =>
        // {{ post.relativeUrl }}
        $content = Strings::replace($content, '#{\$([\w-]+)' . self::PATTERN_ARRAY_ACCESS . '(.*?)}#', '{{ $1.$2$3 }}');

        // {    $post['relativeUrl']    } =>
        // {    post.relativeUrl    }
        $content = Strings::replace(
            $content,
            '#{(.*?)\$?([\w-]+)' . self::PATTERN_ARRAY_ACCESS . '(.*?)}#',
            '{$1$2.$3$4$5}'
        );

        // {$google_analytics_tracking_id} =>
        // {{ google_analytics_tracking_id }}
        // {$google_analytics_tracking_id|someFilter} =>
        // {{ google_analytics_tracking_id|someFilter }}
        $content = Strings::replace($content, '#{\$(\w+)(\|.*?)?}#', '{{ $1$2 }}');

        // {11874|number(0:',':' ')} =>
        // {{ 11874|number(0:',':' ') }}
        $content = Strings::replace($content, '#{(\d+)(\|.*?)}#', '{{ $1$2 }}');

        return $this->processLoopAndConditionsVariables($content);
    }

    private function processLoopAndConditionsVariables(string $content): string
    {
        // {... $variable->someMethodCall() ...}
        // {... variable.someMethodCall() ...}
        $content = Strings::replace(
            $content,
            '#{%(.*?)\$([\w-]+)' . self::PATTERN_METHOD_CALL . '(.*?)%}#',
            '{%$1$2.$3$4%}'
        );

        // {... $variable['someKey'], $variable['anotherKey'] ...}
        // {... variable.someKey, variable.anotherKey ...}
        $content = Strings::replace(
            $content,
            '#{(.*?)}#',
            function (array $match) {
                $match[1] = Strings::replace($match[1], '#' . self::PATTERN_ARRAY_ACCESS . '#', '.$1');

                return '{' . $match[1] . '}';
            }
        );

        // {... $variable ...}
        // {... variable ...}
        return Strings::replace($content, '#{%(.*?)\$([\w-]+)(.*?)%}#', '{%$1$2$3%}');
    }
}
