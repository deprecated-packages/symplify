<?php

declare(strict_types=1);

namespace Symplify\EasyParallel\Tests\CommandLine\Source;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

final class MainCommand extends Command
{
    public function __construct(
        private SomeService $service
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('main');

        $this->addArgument(TestOption::PATHS, InputArgument::IS_ARRAY);
        $this->addOption(TestOption::OUTPUT_FORMAT, null, InputOption::VALUE_REQUIRED | InputOption::VALUE_OPTIONAL);
    }
}
