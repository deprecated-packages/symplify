<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class ConditionCaseConverter implements CaseConverterInterface
{
    public function getPriority(): int
    {
        return 700;
    }

    public function convertContent(string $content): string
    {
        // https://regex101.com/r/XKKoUh/1/
        // {if isset($post['variable'])}...{/if} =>
        // {% if $post['variable'] is defined %}...{% endif %}
        $content = Strings::replace(
            $content,
            '#{if isset\((.*?)\)}(.*?){\/if}#s',
            '{% if $1 is defined %}$2{% endif %}'
        );
        // {ifset $post}...{/ifset} =>
        // {% if $post is defined %}..{% endif %}
        $content = Strings::replace($content, '#{ifset (.*?)}(.*?){\/ifset}#s', '{% if $1 is defined %}$2{% endif %}');

        // {if "sth"}..{/if} =>
        // {% if "sth" %}..{% endif %}
        // https://regex101.com/r/DrDSJf/1
        $content = Strings::replace($content, '#{if (.*?)}(.*?){\/if}#s', '{% if $1 %}$2{% endif %}');

        $content = Strings::replace($content, '#{else}#', '{% else %}');

        return Strings::replace($content, '#{elseif (.*?)}#', '{% elseif $1 %}');
    }
}
