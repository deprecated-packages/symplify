<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Symfony\Rules\RequireNamedCommandRule\Fixture;

use Symfony\Component\Console\Command\Command;

final class SkipNamedCommand extends Command
{
    public function configure()
    {
        $this->setName('named');
    }
}
