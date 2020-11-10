<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\ValueObject\File;
use Symplify\MonorepoBuilder\ValueObject\Option;

abstract class AbstractSymplifyCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    protected $symfonyStyle;

    public function __construct()
    {
        parent::__construct();

        $this->addOption(Option::CONFIG, 'c', InputOption::VALUE_REQUIRED, 'Path to config file', File::CONFIG);
    }

    /**
     * @required
     */
    public function autowireAbstractSymplifyCommand(SymfonyStyle $symfonyStyle): void
    {
        $this->symfonyStyle = $symfonyStyle;
    }
}
