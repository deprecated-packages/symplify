<?php

declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class CaptureCaseConverter implements CaseConverterInterface
{
    /**
     * @see https://regex101.com/r/kmkH4u/1
     * @var string
     */
    private const CAPTURE_REGEX = '#{capture \$(\w+)}(.*?){\/capture}#s';

    /**
     * @see https://regex101.com/r/DjZXne/1
     * @var string
     */
    private const VAR_REGEX = '#{var \$?(.*?) = \$?(.*?)}#s';

    public function getPriority(): int
    {
        return 900;
    }

    public function convertContent(string $content): string
    {
        // {var $var = $anotherVar} =>
        // {% set var = anotherVar %}
        $content = Strings::replace($content, self::VAR_REGEX, '{% set $1 = $2 %}');

        // {capture $var}...{/capture} =>
        // {% set var %}...{% endset %}
        return Strings::replace($content, self::CAPTURE_REGEX, '{% set $1 %}$2{% endset %}');
    }
}
