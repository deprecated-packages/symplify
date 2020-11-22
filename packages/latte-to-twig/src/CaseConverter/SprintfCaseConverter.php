<?php

declare(strict_types=1);

namespace Symplify\LatteToTwig\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwig\Contract\CaseConverter\CaseConverterInterface;

final class SprintfCaseConverter implements CaseConverterInterface
{
    public function getPriority(): int
    {
        return 450;
    }

    public function convertContent(string $content): string
    {
        return Strings::replace($content, '#{%(.*?)sprintf\((.*?), ?(.*?)\)#s', '{%$1$2|format($3)');
    }
}
