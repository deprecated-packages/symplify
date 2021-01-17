<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckOptionArgumentCommandRule\Fixture;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\RuleDocGenerator\ValueObject\Option;

final class IncorrectCommand1 extends Command
{
    protected function configure(): void
    {
        $this->addOption(Option::CATEGORIZE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $shouldCategorize = (bool) $input->getArgument(Option::CATEGORIZE);

        return 0;
    }
}
