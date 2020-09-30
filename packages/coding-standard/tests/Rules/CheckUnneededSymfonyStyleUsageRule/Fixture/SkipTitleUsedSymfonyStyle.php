<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckRequireMethodTobeAutowireWithClassName\Fixture;

use Symfony\Component\Console\Style\SymfonyStyle;

class SkipTitleUsedSymfonyStyle
{
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    public function run()
    {
        $this->symfonyStyle->title('Welcome');
        $this->symfonyStyle->newline();
    }
}
