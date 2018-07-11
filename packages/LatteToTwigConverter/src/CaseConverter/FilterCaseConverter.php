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
        $content = Strings::replace($content, '#{\$([\w-]+)\[\'([\w-]+)\'\]\|([^}]+)}#', '{{ $1.$2|$3 }}');

        // | noescape =>
        // | raw
        $content = Strings::replace($content, '#\|(\s+)?noescape#', '|$1raw');

        // ... count(5) =>
        // ... 5|length
        return Strings::replace($content, '#{(.*?) count\(\$?(\w+)\)(.*?)}#', '{$1 $2|length$3}');
    }
}
