<?php

declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class LoopsCaseConverter implements CaseConverterInterface
{
    /**
     * @see https://regex101.com/r/tPz85Z/1
     * @var string
     */
    private const SEP_WRAPPED_REGEX = '#{sep}(.*?){\/sep}#msi';

    /**
     * @see https://regex101.com/r/8KIqMP/1
     * @var string
     */
    private const LAST_WRAPPED_REGEX = '#{last}(.*?){\/last}#msi';

    /**
     * @see https://regex101.com/r/vvfYpb/1
     * @var string
     */
    private const FIRST_WRAPPED_REGEX = '#{first}(.*?){/first}#msi';

    /**
     * @see https://regex101.com/r/cio5Mq/1
     * @var string
     */
    private const FOREACH_OPEN_REGEX = '#{foreach \$?(.*?) as \$([()\w ]+)}#i';

    /**
     * @see https://regex101.com/r/qykaeL/1
     * @var string
     */
    private const FOREACH_CLOSE_REGEX = '#{\/foreach}#';

    /**
     * @see https://regex101.com/r/5FezKh/1
     * @var string
     */
    private const FOREACH_LIST_REGEX = '#{foreach \$?(?<list>.*?) as (?<items>\[.*?\])}#i';

    /**
     * @see https://regex101.com/r/yFqpaU/1
     * @var string
     */
    private const FOREACH_OPEN_WITH_KEY_REGEX = '#{foreach \$?(.*?) as \$([()\w ]+) => \$(\w+)}#i';

    public function getPriority(): int
    {
        return 400;
    }

    public function convertContent(string $content): string
    {
        /**
         * {foreach $values as $key => $value}...{/foreach}
         * ↓
         *
         * {% for key, value in values %}...{% endfor %}
         */
        $content = Strings::replace($content, self::FOREACH_OPEN_WITH_KEY_REGEX, '{% for $2, $3 in $1 %}');

        /**
         * {foreach $values as [$value1, $value2]}...{/foreach}
         * ↓
         * {% for [value1, value2] in values %}...{% endfor %}
         */
        $content = Strings::replace(
            $content,
            self::FOREACH_LIST_REGEX,
            function (array $match): string {
                $variablesWithoutDollar = str_replace('$', '', $match['items']);
                return sprintf('{%% for %s in %s %%}', $variablesWithoutDollar, $match['list']);
            }
        );

        /**
         * {foreach $values as $value}...{/foreach}
         * ↓
         * {% for value in values %}...{% endfor %}
         */
        $content = Strings::replace($content, self::FOREACH_OPEN_REGEX, '{% for $2 in $1 %}');
        $content = Strings::replace($content, self::FOREACH_CLOSE_REGEX, '{% endfor %}');

        // {first}...{/first} =>
        // {% if loop.first %}...{% endif %}
        $content = Strings::replace($content, self::FIRST_WRAPPED_REGEX, '{% if loop.first %}$1{% endif %}');

        // {last}...{/last} =>
        // {% if loop.last %}...{% endif %}
        $content = Strings::replace($content, self::LAST_WRAPPED_REGEX, '{% if loop.last %}$1{% endif %}');

        // {sep}, {/sep} => {% if loop.last == false %}, {% endif %}
        return Strings::replace($content, self::SEP_WRAPPED_REGEX, '{% if loop.last == false %}$1{% endif %}');
    }
}
