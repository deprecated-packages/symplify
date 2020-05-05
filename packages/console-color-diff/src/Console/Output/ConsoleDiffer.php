<?php

declare(strict_types=1);

namespace Symplify\ConsoleColorDiff\Console\Output;

use SebastianBergmann\Diff\Differ;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ConsoleColorDiff\Console\Formatter\ColorConsoleDiffFormatter;

final class ConsoleDiffer
{
    /**
     * @var Differ
     */
    private $differ;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ColorConsoleDiffFormatter
     */
    private $colorConsoleDiffFormatter;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        Differ $differ,
        ColorConsoleDiffFormatter $colorConsoleDiffFormatter
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->differ = $differ;
        $this->colorConsoleDiffFormatter = $colorConsoleDiffFormatter;
    }

    public function diff(string $old, string $new): void
    {
        $diff = $this->differ->diff($old, $new);
        $consoleFormatted = $this->colorConsoleDiffFormatter->format($diff);
        $this->symfonyStyle->writeln($consoleFormatted);
    }
}
