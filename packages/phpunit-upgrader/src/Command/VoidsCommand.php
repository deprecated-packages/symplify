<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PHPUnitUpgrader\PHPUnitUpgrader\VoidPHPUnitUpgrader;
use Symplify\PHPUnitUpgrader\ValueObject\Option;

final class VoidsCommand extends AbstractSymplifyCommand
{
    public function __construct(
        private VoidPHPUnitUpgrader $voidPHPUnitUpgrader
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Add `void` to `setUp()` and `tearDown()` methods');
        $this->addArgument(Option::SOURCE, InputArgument::REQUIRED, 'Path to tests directory');
        $this->addOption(Option::DRY_RUN, null, InputOption::VALUE_NONE, 'Do no change, only show the diff');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = (string) $input->getArgument(Option::SOURCE);
        $this->fileSystemGuard->ensureDirectoryExists($source);

        $testFileInfos = $this->smartFinder->find([$source], '#Test\.php#');
        $this->voidPHPUnitUpgrader->completeFileInfos($testFileInfos);

        $this->symfonyStyle->success('void is at in all setUp()/tearDown() methods now');

        return self::SUCCESS;
    }
}
