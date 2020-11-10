<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Neon;

use Nette\Neon\Encoder;
use Nette\Neon\Neon;
use Nette\Utils\Strings;

final class NeonPrinter
{
    /**
     * @param mixed[] $phpStanNeon
     */
    public function printNeon(array $phpStanNeon): string
    {
        $neonContent = Neon::encode($phpStanNeon, Encoder::BLOCK);

        // tabs to spaces for consistency
        $neonContent = Strings::replace($neonContent, '#\t#', '    ');

        return rtrim($neonContent) . PHP_EOL;
    }
}
