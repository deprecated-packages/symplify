<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter;

use Nette\Utils\FileSystem;
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
        $content = FileSystem::read($file);

        foreach ($this->caseConverters as $caseConverter) {
            $content = $caseConverter->convertContent($content);
        }

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

//        // conditions:
//        // {% if $post['deprecated'] =>
//        // {% if $post.deprecated
//        // https://regex101.com/r/XKKoUh/2
//        $content = Strings::replace($content, '#{% (\w+) \$([A-Za-z]+)\[\'([\A-Za-z]+)\'\]#', '{% $1 $2.$3');
//
//        $content = Strings::replace($content, '#{else}#', '{% else %}');

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

        $content = Strings::replace($content, '#{% (.*?) count\(\$?(\w+)\)#', '{% $1 $2|length');

        return Strings::replace($content, '#{% include \'?(\w+)\'? %}#', '{{ block(\'$1\') }}');
    }
}
