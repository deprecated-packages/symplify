<?php

declare(strict_types=1);

namespace Symplify\PackageScoper\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\ShellCode;

final class ScopeComposerCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return ShellCode::SUCCESS;
    }
}
