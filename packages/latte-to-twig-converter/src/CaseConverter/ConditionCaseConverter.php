<?php

declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class ConditionCaseConverter implements CaseConverterInterface
{
    /**
     * @see https://regex101.com/r/GTcvvg/1
     * @var string
     */
    private const ELSEIF_REGEX = '#{elseif (.*?)}#';

    /**
     * @see https://regex101.com/r/LWhktx/1
     * @var string
     */
    private const ELSE_REGEX = '#{else}#';

    /**
     * @see https://regex101.com/r/hJJH3a/1
     * @var string
     */
    private const IF_ISSET_REGEX = '#{if isset\((.*?)\)}(.*?){\/if}#s';

    /**
     * @see https://regex101.com/r/TZkZAq/1/
     * @var string
     */
    private const IFSET_REGEX = '#{ifset (.*?)}(.*?){\/ifset}#s';

    /**
     * @see https://regex101.com/r/6WJwLz/1
     * @var string
     */
    private const IF_REGEX = '#{if (.*?)}(.*?){\/if}#s';

    public function getPriority(): int
    {
        return 700;
    }

    public function convertContent(string $content): string
    {
        // https://regex101.com/r/XKKoUh/1/
        // {if isset($post['variable'])}...{/if} =>
        // {% if $post['variable'] is defined %}...{% endif %}
        $content = Strings::replace($content, self::IF_ISSET_REGEX, '{% if $1 is defined %}$2{% endif %}');
        // {ifset $post}...{/ifset} =>
        // {% if $post is defined %}..{% endif %}
        $content = Strings::replace($content, self::IFSET_REGEX, '{% if $1 is defined %}$2{% endif %}');

        // {if "sth"}..{/if} =>
        // {% if "sth" %}..{% endif %}
        // https://regex101.com/r/DrDSJf/1
        $content = Strings::replace($content, self::IF_REGEX, '{% if $1 %}$2{% endif %}');

        $content = Strings::replace($content, self::ELSE_REGEX, '{% else %}');

        return Strings::replace($content, self::ELSEIF_REGEX, '{% elseif $1 %}');
    }
}
