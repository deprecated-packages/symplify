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
    private const PATTERN_METHOD_CALL = '->([\w-()]+)';

    /**
     * @var string
     *
     * Matches:
     * ['someValue']
     */
    private const PATTERN_ARRAY_ACCESS = '\[\'([\w-]+)\'\]';

    public function convertContent(string $content): string
    {
        // {$post->getId()} =>
        // {{ post.getId() }}
        $content = Strings::replace($content, '#{\$([\w-]+)' . self::PATTERN_METHOD_CALL . '(.*?)}#', '{{ $1.$2$3 }}');

        // {$post['relativeUrl']} =>
        // {{ post.relativeUrl }}
        $content = Strings::replace($content, '#{\$([\w-]+)' . self::PATTERN_ARRAY_ACCESS . '(.*?)}#', '{{ $1.$2$3 }}');

        // {$google_analytics_tracking_id} =>
        // {{ google_analytics_tracking_id }}
        // {$google_analytics_tracking_id|someFilter} =>
        // {{ google_analytics_tracking_id|someFilter }}
        $content = Strings::replace($content, '#{\$(\w+)(\|.*?)?}#', '{{ $1$2 }}');

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

        // {... $variable['someKey'] ...}
        // {... variable.someKey ...}
        $content = Strings::replace(
            $content,
            '#{%(.*?)\$([\w-]+)' . self::PATTERN_ARRAY_ACCESS . '(.*?)%}#',
            '{%$1$2.$3$4%}'
        );

        // {... $variable ...}
        // {... variable ...}
        return Strings::replace($content, '#{%(.*?)\$([\w-]+)(.*?)%}#', '{%$1$2$3%}');
    }
}
