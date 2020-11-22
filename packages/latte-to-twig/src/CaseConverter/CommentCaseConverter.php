<?php

declare(strict_types=1);

namespace Symplify\LatteToTwig\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwig\Contract\CaseConverter\CaseConverterInterface;

final class CommentCaseConverter implements CaseConverterInterface
{
    public function getPriority(): int
    {
        return 800;
    }

    public function convertContent(string $content): string
    {
        return Strings::replace($content, '#{\*(.*?)\*}#s', '{#$1#}');
    }
}
