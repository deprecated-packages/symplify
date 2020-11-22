<?php

declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class NClassMacroCaseConverter implements CaseConverterInterface
{
    /**
     * @see https://regex101.com/r/PVBgmO/1
     * @var string
     */
    private const N_CLASS_REGEX = '#n:class="\$?(.*?)\s+\?\s+(?<value>(.*?))"#';

    public function getPriority(): int
    {
        return 1600;
    }

    public function convertContent(string $content): string
    {
        return Strings::replace(
            $content,
            // n:class="$cond ? active"
            self::N_CLASS_REGEX,
            'class="{% if $1 %}$2{% endif %}"'
        );
    }
}
