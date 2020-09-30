<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckUnneededSymfonyStyleUsageRule\Fixture;

use Symfony\Component\Console\Style\SymfonyStyle;

class SkipChildOfSymfonyStyle extends SymfonyStyle
{
    public function run()
    {
        $this->newline();
    }
}
