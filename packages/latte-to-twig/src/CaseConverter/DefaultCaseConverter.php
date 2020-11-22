<?php

declare(strict_types=1);

namespace Symplify\LatteToTwig\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwig\Contract\CaseConverter\CaseConverterInterface;

/**
 * @see https://latte.nette.org/en/macros#toc-variable-declaration
 */
final class DefaultCaseConverter implements CaseConverterInterface
{
    public function getPriority(): int
    {
        return 600;
    }

    public function convertContent(string $content): string
    {
        return Strings::replace(
            $content,
            '#{default \$?(.*?) = \$?(.*?)}#s',
            '{% if $1 is not defined %}{% set $1 = $2 %}{% endif %}'
        );
    }
}
