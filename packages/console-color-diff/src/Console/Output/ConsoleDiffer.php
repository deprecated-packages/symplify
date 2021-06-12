<?php

declare(strict_types=1);

namespace Symplify\ConsoleColorDiff\Console\Output;

use SebastianBergmann\Diff\Differ;
use Symplify\ConsoleColorDiff\Console\Formatter\ColorConsoleDiffFormatter;

final class ConsoleDiffer
{
    public function __construct(
        private Differ $differ,
        private ColorConsoleDiffFormatter $colorConsoleDiffFormatter
    ) {
    }

    public function diff(string $old, string $new): string
    {
        $diff = $this->differ->diff($old, $new);
        return $this->colorConsoleDiffFormatter->format($diff);
    }
}
