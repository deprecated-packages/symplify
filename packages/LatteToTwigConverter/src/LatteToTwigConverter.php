<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class LatteToTwigConverter
{
    /**
     * @var CaseConverterInterface[]
     */
    private $caseConverters = [];

    public function addCaseConverter(CaseConverterInterface $caseConverter): void
    {
        $this->caseConverters[] = $caseConverter;
    }

    public function convertFile(string $file): string
    {
        $content = file_get_contents($file);

        // block/include:
        // {block content}...{/block} =>
        // {% block content %}...{% endblock %}
        $content = Strings::replace($content, '#{block (\w+)}(.*?){\/block}#s', '{% block $1 %}$2{% endblock %}');
        // {include "_snippets/menu.latte"} =>
        // {% include "_snippets/menu.latte" %}
        $content = Strings::replace($content, '#{include ([^}]+)}#', '{% include $1 %}');
        // {define sth}...{/define} =>
        // {% block sth %}...{% endblock %}
        $content = Strings::replace($content, '#{define (.*?)}(.*?){\/define}#s', '{% block $1 %}$2{% endblock %}');

        // variables:
        // {$google_analytics_tracking_id} =>
        // {{ google_analytics_tracking_id }}
        // {$google_analytics_tracking_id|someFilter} =>
        // {{ google_analytics_tracking_id|someFilter }}
        $content = Strings::replace($content, '#{\$(\w+)(\|.*?)?}#', '{{ $1$2 }}');
        // {$post->getId()} =>
        // {{ post.getId() }}
        $content = Strings::replace($content, '#{\$([\w]+)->([\w()]+)}#', '{{ $1.$2 }}');
        // {$post['relativeUrl']} =>
        // {{ post.relativeUrl }}
        $content = Strings::replace($content, '#{\$([A-Za-z_-]+)\[\'([A-Za-z_-]+)\'\]}#', '{{ $1.$2 }}');

        // suffix: "_snippets/menu.latte" => "_snippets/menu.twig"
        $content = Strings::replace($content, '#([A-Za-z_/"]+).latte#', '$1.twig');

        // include var:
        // {% include "_snippets/menu.latte", "data" => $data %} =>
        // {% include "_snippets/menu.twig", { "data": data } %}
        // see https://twig.symfony.com/doc/2.x/functions/include.html
        // single lines
        // ref https://regex101.com/r/uDJaia/1
        $content = Strings::replace($content, '#({% include [^,]+,)([^}^:]+)(\s+%})#', function (array $match) {
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

        // filter:
        // {$post['updated_message']|noescape} =>
        // {{ post.updated_message|noescape }}
        $content = Strings::replace($content, '#{\$([A-Za-z_-]+)\[\'([A-Za-z_-]+)\'\]\|([^}]+)}#', '{{ $1.$2|$3 }}');

        // loops:
        // {sep}, {/sep} => {% if loop.last == false %}, {% endif %}
        $content = Strings::replace($content, '#{sep}([^{]+){\/sep}#', '{% if loop.last == false %}$1{% endif %}');

        // conditions:
        // https://regex101.com/r/XKKoUh/1/
        // {if isset($post['variable'])}...{/if} =>
        // {% if $post['variable'] is defined %}...{% endif %}
        $content = Strings::replace(
            $content,
            '#{if isset\(([^{]+)\)}(.*?){\/if}#s',
            '{% if $1 is defined %}$2{% endif %}'
        );
        // {ifset $post}...{/ifset} =>
        // {% if $post is defined %}..{% endif %}
        $content = Strings::replace($content, '#{ifset (.*?)}(.*?){\/ifset}#s', '{% if $1 is defined %}$2{% endif %}');

        // {% if $post['deprecated'] =>
        // {% if $post.deprecated
        // https://regex101.com/r/XKKoUh/2
        $content = Strings::replace($content, '#{% (\w+) \$([A-Za-z]+)\[\'([\A-Za-z]+)\'\]#', '{% $1 $2.$3');

        $content = Strings::replace($content, '#{else}#', '{% else %}');

        // {var $var = $anotherVar} => {% set var = anotherVar %}
        $content = Strings::replace($content, '#{var \$?(.*?) = (.*?)}#s', '{% set $1 = $2 %}');

        // {capture $var}...{/capture} => {% set var %}...{% endset %}
        $content = Strings::replace($content, '#{capture \$(\w+)}(.*?){\/capture}#s', '{% set $1 %}$2{% endset %}');

//     {% if $post['rectify_post_id'] is defined %} => {% if post.rectify_post_id is defined %}
        $content = Strings::replace($content, '#({% \w+) \$(\w+)\[\'(\w+)\'\]#', '$1 $2.$3');

        // | noescape }=> | raw
        $content = Strings::replace($content, '#\| noescape#', '| raw');

        // {% include "sth", = {% include "sth" with
        $content = Strings::replace($content, '#({% include [^,{]+)(,)#', '$1 with');

        // {if "sth"}..{/if} =>  {% if "sth" %}..{% endif %} =>
        $content = Strings::replace($content, '#{if ([($)\w]+)}(.*?){\/if}#s', '{% if $1 %}$2{% endif %}');
        // {foreach $values as $key => $value}...{/foreach} => {% for key, value in values %}...{% endfor %}
        $content = Strings::replace(
            $content,
            '#{foreach \$([()\w ]+) as \$([()\w ]+) => \$(\w+)}#',
            '{% for $2, $3 in $1 %}'
        );
        // {foreach $values as $value}...{/foreach} => {% for value in values %}...{% endfor %}
        $content = Strings::replace($content, '#{foreach \$([()\w ]+) as \$([()\w ]+)}#', '{% for $2 in $1 %}');
        $content = Strings::replace($content, '#{/foreach}#', '{% endfor %}');

        // {foreach ...)...{/foreach} =>
        $content = Strings::replace($content, '#{% (.*?) count\(\$?(\w+)\)#', '{% $1 $2|length');

        // fixes "%)" => "%}"
        $content = Strings::replace($content, '#%\)#', '%}');

        $content = Strings::replace($content, '#{% include \'?(\w+)\'? %}#', '{{ block(\'$1\') }}');

        return Strings::replace($content, '#{\* (.*?) \*}#s', '{# $1 #}');
    }
}
