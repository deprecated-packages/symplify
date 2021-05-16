<?php

declare(strict_types=1);

namespace Symplify\ConsoleColorDiff\Console\Output;

use SebastianBergmann\Diff\Differ;
use Symplify\ConsoleColorDiff\Console\Formatter\ColorConsoleDiffFormatter;

final class ConsoleDiffer
{
    /**
     * @var Differ
     */
    private $differ;

    /**
     * @var ColorConsoleDiffFormatter
     */
    private $colorConsoleDiffFormatter;

    public function __construct(Differ $differ, ColorConsoleDiffFormatter $colorConsoleDiffFormatter)
    {
        $this->differ = $differ;
        $this->colorConsoleDiffFormatter = $colorConsoleDiffFormatter;
    }

    public function diff(string $old, string $new): string
    {
        $diff = $this->differ->diff($old, $new);
        return $this->colorConsoleDiffFormatter->format($diff);
    }
}
