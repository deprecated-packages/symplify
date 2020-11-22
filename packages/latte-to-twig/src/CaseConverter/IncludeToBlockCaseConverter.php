<?php

declare(strict_types=1);

namespace Symplify\LatteToTwig\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwig\Contract\CaseConverter\CaseConverterInterface;

final class IncludeToBlockCaseConverter implements CaseConverterInterface
{
    /**
     * @see https://regex101.com/r/7QZ3sD/2
     * @var string
     */
    private const INCLUDE_REGEX = '#{% include \'?(\w+)\'? %}#';

    public function getPriority(): int
    {
        return 100;
    }

    public function convertContent(string $content): string
    {
        return Strings::replace($content, self::INCLUDE_REGEX, '{{ block(\'$1\') }}');
    }
}
