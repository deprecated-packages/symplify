<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class FilterCaseConverter implements CaseConverterInterface
{
    public function convertContent(string $content): string
    {
        // {$post['updated_message']|noescape} =>
        // {{ post.updated_message|noescape }}
        $content = Strings::replace($content, '#{\$([A-Za-z_-]+)\[\'([A-Za-z_-]+)\'\]\|([^}]+)}#', '{{ $1.$2|$3 }}');

        // | noescape =>
        // | raw
        return Strings::replace($content, '#\|(\s+)?noescape#', '|$1raw');
    }
}
