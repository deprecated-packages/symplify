<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class VariableCaseConverter implements CaseConverterInterface
{
    public function convertContent(string $content): string
    {
        // {$google_analytics_tracking_id} =>
        // {{ google_analytics_tracking_id }}
        // {$google_analytics_tracking_id|someFilter} =>
        // {{ google_analytics_tracking_id|someFilter }}
        $content = Strings::replace($content, '#{\$(\w+)(\|.*?)?}#', '{{ $1$2 }}');

        // {$post->getId()} =>
        // {{ post.getId() }}
        $content = Strings::replace($content, '#{(.*?)\$([\w]+)->([\w()]+)(.*?)}#', '{$1$2.$3$4}');

        // {$post['relativeUrl']} =>
        // {{ post.relativeUrl }}
        $content = Strings::replace($content, '#{(.*?)\$([\w-]+)\[\'([\w-]+)\'\](.*?)}#', '{$1$2.$3$4}');

        // {... $variable ...}
        // {... variable ...}
        return Strings::replace($content, '#{(.*?)\$([\w-]+)(.*?)}#', '{$1$2$3}');
    }
}
