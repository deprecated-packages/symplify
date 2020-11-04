<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenPrivateMethodByTypeRule\Fixture;

use Symfony\Component\Console\Command\Command;

class ConsoleCommand extends Command
{
    private function foo()
    {
    }
}
