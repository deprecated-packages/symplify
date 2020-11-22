<?php

declare(strict_types=1);

namespace Symplify\LatteToTwig\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwig\Contract\CaseConverter\CaseConverterInterface;

final class SprintfCaseConverter implements CaseConverterInterface
{
    /**
     * @see https://regex101.com/r/oMunbj/1
     * @var string
     */
    private const SPRINTF_REGEX = '#{%(.*?)sprintf\((.*?), ?(.*?)\)#s';

    public function getPriority(): int
    {
        return 450;
    }

    public function convertContent(string $content): string
    {
        return Strings::replace($content, self::SPRINTF_REGEX, '{%$1$2|format($3)');
    }
}
