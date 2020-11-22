<?php

declare(strict_types=1);

namespace Symplify\LatteToTwig\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwig\Contract\CaseConverter\CaseConverterInterface;

final class SuffixCaseConverter implements CaseConverterInterface
{
    public function getPriority(): int
    {
        return 150;
    }

    public function convertContent(string $content): string
    {
        // suffix: "_snippets/menu.latte" => "_snippets/menu.twig"
        return Strings::replace($content, '#([\w/"]+).latte#', '$1.twig');
    }
}
