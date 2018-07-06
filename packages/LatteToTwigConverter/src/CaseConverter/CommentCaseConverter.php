<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class CommentCaseConverter implements CaseConverterInterface
{
    public function convertContent(string $content): string
    {
        return Strings::replace($content, '#{\*(.*?)\*}#s', '{#$1#}');
    }
}
