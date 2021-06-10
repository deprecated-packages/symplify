<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\CheckUnneededSymfonyStyleUsageRule\Fixture;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SkipInCommand extends Command
{
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->symfonyStyle->write('Hi');
    }
}
