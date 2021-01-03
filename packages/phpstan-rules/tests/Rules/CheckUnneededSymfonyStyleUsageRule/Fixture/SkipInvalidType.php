<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckUnneededSymfonyStyleUsageRule\Fixture;

class SkipInvalidType
{
    private $symfonyStyle;

    public function __construct($symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    public function run()
    {
        $this->symfonyStyle->newline();
    }
}
