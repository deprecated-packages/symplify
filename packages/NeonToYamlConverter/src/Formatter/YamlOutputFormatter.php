<?php declare(strict_types=1);

namespace Symplify\NeonToYamlConverter\Formatter;

use Nette\Utils\Strings;
use Symplify\PackageBuilder\Configuration\EolConfiguration;

final class YamlOutputFormatter
{
    public function format(string $content): string
    {
        $eolChar = EolConfiguration::getEolChar();

        // new lines between main parts
        $content = Strings::replace($content, '#^(\w)#ms', $eolChar . '$1');

        // factory inline
        $content = Strings::replace($content, '#factory:\s+- (.*?)\n\s+- (.*?)\n#ms', 'factory: [$1, $2]' . $eolChar);

        // calls inline fixup
        $content = Strings::replace($content, '#(calls|imports):\s\|#', '$1:');

        return trim($content) . $eolChar;
    }
}
