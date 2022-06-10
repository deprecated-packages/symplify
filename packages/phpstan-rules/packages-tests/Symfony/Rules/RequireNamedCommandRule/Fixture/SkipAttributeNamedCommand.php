<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Symfony\Rules\RequireNamedCommandRule\Fixture;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(name: 'named')]
final class SkipAttributeNamedCommand extends Command
{
    public function configure()
    {
    }
}
