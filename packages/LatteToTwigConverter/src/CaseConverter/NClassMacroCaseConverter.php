<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class NClassMacroCaseConverter implements CaseConverterInterface
{
    public function getPriority(): int
    {
        return 1600;
    }

    public function convertContent(string $content): string
    {
        return Strings::replace(
            $content,
            // n:class="$cond ? active"
            '#n:class="\$?(.*?)\s+\?\s+(?<value>(.*?))"#',
            'class="{% if $1 %}$2{% endif %}"'
        );
    }
}
