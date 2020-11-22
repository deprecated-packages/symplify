<?php

declare(strict_types=1);

namespace Symplify\LatteToTwig\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwig\Contract\CaseConverter\CaseConverterInterface;

final class BlockCaseConverter implements CaseConverterInterface
{
    /**
     * @see https://regex101.com/r/cUWPWT/1
     * @var string
     */
    private const INCLUDE_REGEX = '#({% include .*?,)(.*?)(\s+%})#';

    /**
     * @see https://regex101.com/r/P02bCD/1
     * @var string
     */
    private const DEFINE_REGEX = '#{define (.*?)}(.*?){\/define}#s';

    /**
     * @see https://regex101.com/r/WK5Rj5/1
     * @var string
     */
    private const BLOCK_REGEX = '#{block (\w+)}(.*?){\/block}#s';

    /**
     * @see https://regex101.com/r/tdN2a8/1
     * @var string
     */
    private const INCLUDE_WITH_COMMA_REGEX = '#({% include [^,{}]+)(,)#';

    /**
     * @see https://regex101.com/r/1PB1RT/1
     * @var string
     */
    private const INCLUDE_EXTENDS_REGEX = '#{(include|extends) ([^}]+)}#';

    public function getPriority(): int
    {
        return 1000;
    }

    public function convertContent(string $content): string
    {
        // block/include:
        // {block content}...{/block} =>
        // {% block content %}...{% endblock %}
        $content = Strings::replace($content, self::BLOCK_REGEX, '{% block $1 %}$2{% endblock %}');
        // {include "_snippets/menu.latte"} =>
        // {% include "_snippets/menu.latte" %}
        // {extends "_snippets/menu.latte"} =>
        // {% extends "_snippets/menu.latte" %}
        $content = Strings::replace($content, self::INCLUDE_EXTENDS_REGEX, '{% $1 $2 %}');
        // {define sth}...{/define} =>
        // {% block sth %}...{% endblock %}
        $content = Strings::replace($content, self::DEFINE_REGEX, '{% block $1 %}$2{% endblock %}');

        // include var:
        // {% include "_snippets/menu.latte", "data" => $data %} =>
        // {% include "_snippets/menu.twig", { "data": data } %}
        // see https://twig.symfony.com/doc/2.x/functions/include.html
        // single lines
        // ref https://regex101.com/r/uDJaia/1
        $content = Strings::replace($content, self::INCLUDE_REGEX, function (array $match): string {
            $variables = explode(',', $match[2]);

            $twigDataInString = ' { ';
            $variableCount = count($variables);
            foreach ($variables as $i => $variable) {
                [
                 $key, $value,
                ] = explode('=>', $variable);
                $key = trim($key);
                $value = trim($value);
                // variables do not start with
                $value = ltrim($value, '$');

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
        return Strings::replace($content, self::INCLUDE_WITH_COMMA_REGEX, '$1 with');
    }
}
