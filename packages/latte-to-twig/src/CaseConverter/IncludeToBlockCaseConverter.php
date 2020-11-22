<?php

declare(strict_types=1);

namespace Symplify\LatteToTwig\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwig\Contract\CaseConverter\CaseConverterInterface;

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
