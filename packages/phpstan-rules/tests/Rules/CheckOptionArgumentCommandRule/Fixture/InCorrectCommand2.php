<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckOptionArgumentCommandRule\Fixture;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\RuleDocGenerator\ValueObject\Option;

final class InCorrectCommand2 extends Command
{
    protected function configure(): void
    {
        $this->addArgument(Option::SOURCES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to directories or files to check');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $shouldCategorize = $input->getOption(Option::SOURCES);

        return 0;
    }
}
