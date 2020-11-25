<?php

declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class DateCaseConverter implements CaseConverterInterface
{
    /**
     * @see https://regex101.com/r/lM2Tmt/1/
     * @var string
     */
    private const DATE_FUNC_CALL_REGEX = '#(({%|{{).*?) date\((.*?)\)#s';

    public function getPriority(): int
    {
        return 420;
    }

    public function convertContent(string $content): string
    {
        return Strings::replace($content, self::DATE_FUNC_CALL_REGEX, '$1 "now"|date($3)');
    }
}
