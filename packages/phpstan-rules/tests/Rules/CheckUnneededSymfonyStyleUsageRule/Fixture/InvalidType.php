<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckUnneededSymfonyStyleUsageRule\Fixture;

class InvalidType
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
