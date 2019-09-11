<?php declare(strict_types=1);

namespace Symplify\NeonToYamlConverter\Formatter;

use Nette\Utils\Strings;

final class YamlOutputFormatter
{
    public function format(string $content): string
    {
        // new lines between main parts
        $content = Strings::replace($content, '#^(\w)#ms', PHP_EOL . '$1');

        // factory inline
        $content = Strings::replace($content, '#factory:\s+- (.*?)\n\s+- (.*?)\n#ms', 'factory: [$1, $2]' . PHP_EOL);

        // calls inline fixup
        $content = Strings::replace($content, '#(calls|imports):\s\|#', '$1:');

        return trim($content) . PHP_EOL;
    }
}
