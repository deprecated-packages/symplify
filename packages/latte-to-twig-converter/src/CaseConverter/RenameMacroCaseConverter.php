<?php

declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class RenameMacroCaseConverter implements CaseConverterInterface
{
    /**
     * @see https://regex101.com/r/gqMf7p/1
     * @var string
     */
    private const SPACELESS_REGEX = '#{spaceless}(.*?){\/spaceless}#ms';

    public function getPriority(): int
    {
        return 300;
    }

    public function convertContent(string $content): string
    {
        return Strings::replace($content, self::SPACELESS_REGEX, '{% spaceless %}$1{% endspaceless %}');
    }
}
