<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class BlockCaseConverter implements CaseConverterInterface
{
    public function getPriority(): int
    {
        return 1000;
    }

    public function convertContent(string $content): string
    {
        // block/include:
        // {block content}...{/block} =>
        // {% block content %}...{% endblock %}
        $content = Strings::replace($content, '#{block (\w+)}(.*?){\/block}#s', '{% block $1 %}$2{% endblock %}');
        // {include "_snippets/menu.latte"} =>
        // {% include "_snippets/menu.latte" %}
        // {extends "_snippets/menu.latte"} =>
        // {% extends "_snippets/menu.latte" %}
        $content = Strings::replace($content, '#{(include|extends) ([^}]+)}#', '{% $1 $2 %}');
        // {define sth}...{/define} =>
        // {% block sth %}...{% endblock %}
        $content = Strings::replace($content, '#{define (.*?)}(.*?){\/define}#s', '{% block $1 %}$2{% endblock %}');

        // include var:
        // {% include "_snippets/menu.latte", "data" => $data %} =>
        // {% include "_snippets/menu.twig", { "data": data } %}
        // see https://twig.symfony.com/doc/2.x/functions/include.html
        // single lines
        // ref https://regex101.com/r/uDJaia/1
        $content = Strings::replace($content, '#({% include .*?,)(.*?)(\s+%})#', function (array $match) {
            $variables = explode(',', $match[2]);

            $twigDataInString = ' { ';
            $variableCount = count($variables);
            foreach ($variables as $i => $variable) {
                [$key, $value] = explode('=>', $variable);
                $key = trim($key);
                $value = trim($value);
                $value = ltrim($value, '$'); // variables do not start with

                $twigDataInString .= $key . ': ' . $value;

                // separator
                if ($i < $variableCount - 1) {
                    $twigDataInString .= ', ';
                }
            }

            $twigDataInString .= ' }';

            return $match[1] . $twigDataInString . $match[3];
        });

        // {% include "sth", =>
        // {% include "sth" with
        return Strings::replace($content, '#({% include [^,{}]+)(,)#', '$1 with');
    }
}
