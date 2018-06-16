<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console\Formatter;

use Nette\Utils\Strings;

final class DumpMergesFormatter
{
    public function format(string $content): string
    {
        // 2 lines from the start
        $content = Strings::replace($content, '#^(\n){2,}#', PHP_EOL);

        // 3 lines to 2
        return Strings::replace($content, '#(\n){3,}#', PHP_EOL . PHP_EOL);
    }
}
