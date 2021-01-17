<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckOptionArgumentCommandRule\Fixture;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\RuleDocGenerator\ValueObject\Option;

final class SkipCorrectSelfConstantCommand extends Command
{
    private const SOURCE = 'source';

    protected function configure(): void
    {
        $this->addOption(self::SOURCE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $shouldCategorize = (bool) $input->getOption(self::SOURCE);
        return 0;
    }
}
