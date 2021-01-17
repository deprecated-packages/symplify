<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckOptionArgumentCommandRule\Fixture;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\RuleDocGenerator\ValueObject\Option;

final class NonExecuteClassMethodCommand extends Command
{
    protected function configure(): void
    {
        $this->addOption(Option::CATEGORIZE);
    }

    public function run(InputInterface $input, OutputInterface $output): int
    {
        $shouldCategorize = $input->getArgument(Option::CATEGORIZE);

        return 0;
    }
}
