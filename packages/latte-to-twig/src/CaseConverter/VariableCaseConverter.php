<?php

declare(strict_types=1);

namespace Symplify\LatteToTwig\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwig\Contract\CaseConverter\CaseConverterInterface;

final class VariableCaseConverter implements CaseConverterInterface
{
    /**
     * @var string
     *
     * Matches:
     * ->someMethodCall()
     */
    private const METHOD_CALL_REGEX = '->([\w\-\(\)]+)';

    /**
     * @var string
     *
     * Matches:
     * ['someValue']
     */
    private const ARRAY_ACCESS_REGEX = '\[\'([\w\-]+)\'\]';

    /**
     * @see https://regex101.com/r/eI65Lu/1
     * @var string
     */
    private const SCRIPT_CONTENT_REGEX = '#<script(.*?)>(.*?)<\/script>#s';

    /**
     * @see https://regex101.com/r/f4Grxb/1
     * @var string
     */
    private const LATTE_VARIABLE_REGEX = '#{%(.*?)\$([\w-]+)(.*?)%}#';

    /**
     * @see https://regex101.com/r/HH0Ech/1
     * @var string
     */
    private const IN_BRACKET_REGEX = '#{(.*?)}#';

    /**
     * @see https://regex101.com/r/zVUFf4/1
     * @var string
     */
    private const VARIABLE_WITH_FILTER_REGEX = '#{(\d+)(\|.*?)}#';

    /**
     * @see https://regex101.com/r/PxWUpY/1
     * @var string
     */
    private const VARIABLE_REGEX = '#{\$(\w+)(\|.*?)?}#';

    /**
     * @see https://regex101.com/r/IN67cX/1
     * @var string
     */
    private const VARIABLE_MATCH_REGEX = '#({\$(.*?)})#';

    public function getPriority(): int
    {
        return 200;
    }

    public function convertContent(string $content): string
    {
        // quote in-script variables, they're auto-quoted by Latte
        $content = Strings::replace($content, self::SCRIPT_CONTENT_REGEX, function (array $match): string {
            $match[2] = Strings::replace($match[2], self::VARIABLE_MATCH_REGEX, '\'$1\'');
            return sprintf('<script%s>%s</script>', $match[1], $match[2]);
        });

        // {$post->getId()} =>
        // {{ post.getId() }}
        $content = Strings::replace($content, '#{\$([\w-]+)' . self::METHOD_CALL_REGEX . '(.*?)}#', '{{ $1.$2$3 }}');

        // {$post['relativeUrl']} =>
        // {{ post.relativeUrl }}
        $content = Strings::replace($content, '#{\$([\w-]+)' . self::ARRAY_ACCESS_REGEX . '(.*?)}#', '{{ $1.$2$3 }}');

        // {    $post['relativeUrl']    } =>
        // {    post.relativeUrl    }
        $content = Strings::replace(
            $content,
            '#{(.*?)\$?([\w-]+)' . self::ARRAY_ACCESS_REGEX . '(.*?)}#',
            '{$1$2.$3$4$5}'
        );

        // {$google_analytics_tracking_id} =>
        // {{ google_analytics_tracking_id }}
        // {$google_analytics_tracking_id|someFilter} =>
        // {{ google_analytics_tracking_id|someFilter }}
        $content = Strings::replace($content, self::VARIABLE_REGEX, '{{ $1$2 }}');

        // {11874|number(0:',':' ')} =>
        // {{ 11874|number(0:',':' ') }}
        $content = Strings::replace($content, self::VARIABLE_WITH_FILTER_REGEX, '{{ $1$2 }}');

        return $this->processLoopAndConditionsVariables($content);
    }

    private function processLoopAndConditionsVariables(string $content): string
    {
        // {... $variable->someMethodCall() ...}
        // {... variable.someMethodCall() ...}
        $content = Strings::replace(
            $content,
            '#{%(.*?)\$([\w-]+)' . self::METHOD_CALL_REGEX . '(.*?)%}#',
            '{%$1$2.$3$4%}'
        );

        // {... $variable['someKey'], $variable['anotherKey'] ...}
        // {... variable.someKey, variable.anotherKey ...}
        $content = Strings::replace(
            $content,
            self::IN_BRACKET_REGEX,
            function (array $match): string {
                $match[1] = Strings::replace($match[1], '#' . self::ARRAY_ACCESS_REGEX . '#', '.$1');
                return '{' . $match[1] . '}';
            }
        );

        // {%... $variable ...%}
        // {%... variable ...%}
        return Strings::replace($content, self::LATTE_VARIABLE_REGEX, '{%$1$2$3%}');
    }
}
