<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class LoopsCaseConverter implements CaseConverterInterface
{
    public function convertContent(string $content): string
    {
        // {foreach $values as $key => $value}...{/foreach} =>
        // {% for key, value in values %}...{% endfor %}
        $content = Strings::replace(
            $content,
            '#{foreach \$([()\w ]+) as \$([()\w ]+) => \$(\w+)}#',
            '{% for $2, $3 in $1 %}'
        );

        // {foreach $values as $value}...{/foreach} =>
        // {% for value in values %}...{% endfor %}
        $content = Strings::replace($content, '#{foreach \$([()\w ]+) as \$([()\w ]+)}#', '{% for $2 in $1 %}');
        $content = Strings::replace($content, '#{/foreach}#', '{% endfor %}');

        // {sep}, {/sep} => {% if loop.last == false %}, {% endif %}
        return Strings::replace($content, '#{sep}([^{]+){\/sep}#', '{% if loop.last == false %}$1{% endif %}');
    }
}
