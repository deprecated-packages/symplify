<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class BlockCaseConverter implements CaseConverterInterface
{
    public function convertContent(string $content): string
    {
        // block/include:
        // {block content}...{/block} =>
        // {% block content %}...{% endblock %}
        $content = Strings::replace($content, '#{block (\w+)}(.*?){\/block}#s', '{% block $1 %}$2{% endblock %}');
        // {include "_snippets/menu.latte"} =>
        // {% include "_snippets/menu.latte" %}
        $content = Strings::replace($content, '#{include ([^}]+)}#', '{% include $1 %}');
        // {define sth}...{/define} =>
        // {% block sth %}...{% endblock %}
        return Strings::replace($content, '#{define (.*?)}(.*?){\/define}#s', '{% block $1 %}$2{% endblock %}');
    }
}
