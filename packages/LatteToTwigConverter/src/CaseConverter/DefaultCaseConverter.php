<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

/**
 * @see https://latte.nette.org/en/macros#toc-variable-declaration
 */
final class DefaultCaseConverter implements CaseConverterInterface
{
    public function convertContent(string $content): string
    {
        return Strings::replace(
            $content,
            '#{default \$?(.*?) = \$?(.*?)}#s',
            '{% if $1 is not defined %}{% set $1 = $2 %}{% endif %}'
        );
    }
}
