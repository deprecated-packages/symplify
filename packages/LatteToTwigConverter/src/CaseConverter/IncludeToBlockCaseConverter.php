<?php

declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class IncludeToBlockCaseConverter implements CaseConverterInterface
{
    public function getPriority(): int
    {
        return 100;
    }

    public function convertContent(string $content): string
    {
        return Strings::replace($content, '#{% include \'?(\w+)\'? %}#', '{{ block(\'$1\') }}');
    }
}
