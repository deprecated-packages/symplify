<?php

declare(strict_types=1);

namespace Symplify\LatteToTwig\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwig\Contract\CaseConverter\CaseConverterInterface;

final class CommentCaseConverter implements CaseConverterInterface
{
    /**
     * @see https://regex101.com/r/LknXm2/1
     * @var string
     */
    private const COMMENT_REGEX = '#{\*(.*?)\*}#s';

    public function getPriority(): int
    {
        return 800;
    }

    public function convertContent(string $content): string
    {
        return Strings::replace($content, self::COMMENT_REGEX, '{#$1#}');
    }
}
