<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class DateCaseConverter implements CaseConverterInterface
{
    public function getPriority(): int
    {
        return 420;
    }

    public function convertContent(string $content): string
    {
        return Strings::replace($content, '#(({%|{{).*?) date\((.*?)\)#s', '$1 "now"|date($3)');
    }
}
