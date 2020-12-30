<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ClassNameRespectsParentSuffixRule\Fixture;

use Symfony\Component\Console\Command\Command;

class SkipAnonymousClass
{
    public function run()
    {
        $someClass = new class extends Command
        {
        };
    }
}
