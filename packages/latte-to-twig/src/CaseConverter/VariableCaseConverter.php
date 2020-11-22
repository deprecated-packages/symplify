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

    public function getPriority(): int
    {
        return 200;
    }

    public function convertContent(string $content): string
    {
        // quote in-script variables, they're auto-quoted by Latte
        $content = Strings::replace($content, '#<script(.*?)>(.*?)</script>#s', function (array $match): string {
            $match[2] = Strings::replace($match[2], '#({\$(.*?)})#', '\'$1\'');

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
            '#{%(.*?)\$([\w-]+)' . self::METHOD_CALL_REGEX . '(.*?)%}#',
            '{%$1$2.$3$4%}'
        );

        // {... $variable['someKey'], $variable['anotherKey'] ...}
        // {... variable.someKey, variable.anotherKey ...}
        $content = Strings::replace(
            $content,
            '#{(.*?)}#',
            function (array $match): string {
                $match[1] = Strings::replace($match[1], '#' . self::ARRAY_ACCESS_REGEX . '#', '.$1');

                return '{' . $match[1] . '}';
            }
        );

        // {... $variable ...}
        // {... variable ...}
        return Strings::replace($content, '#{%(.*?)\$([\w-]+)(.*?)%}#', '{%$1$2$3%}');
    }
}
