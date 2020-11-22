<?php

declare(strict_types=1);

namespace Symplify\LatteToTwig\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwig\Contract\CaseConverter\CaseConverterInterface;

final class RenameMacroCaseConverter implements CaseConverterInterface
{
    public function getPriority(): int
    {
        return 300;
    }

    public function convertContent(string $content): string
    {
        return Strings::replace($content, '#{spaceless}(.*?){/spaceless}#ms', '{% spaceless %}$1{% endspaceless %}');
    }
}
