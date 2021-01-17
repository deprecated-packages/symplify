<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckOptionArgumentCommandRule\Fixture;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class IncorrectCommand2 extends Command
{
    protected function configure(): void
    {
        $this->addArgument('sources');
        $this->addOption('enabled');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $shouldCategorize = $input->getOption('sources');
        $enabled = $input->getArgument('enabled');

        return 0;
    }
}
