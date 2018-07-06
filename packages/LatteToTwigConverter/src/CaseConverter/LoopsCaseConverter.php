<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class LoopsCaseConverter implements CaseConverterInterface
{
    public function convertContent(string $content): string
    {
        // {sep}, {/sep} => {% if loop.last == false %}, {% endif %}
        return Strings::replace($content, '#{sep}([^{]+){\/sep}#', '{% if loop.last == false %}$1{% endif %}');
    }
}
