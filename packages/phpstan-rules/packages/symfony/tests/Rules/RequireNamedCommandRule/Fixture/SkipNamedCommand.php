<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\RequireNamedCommandRule\Fixture;

use Symfony\Component\Console\Command\Command;

final class SkipNamedCommand extends Command
{
    public function configure()
    {
        $this->setName('named');
    }
}
