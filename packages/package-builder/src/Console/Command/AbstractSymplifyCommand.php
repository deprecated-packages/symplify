<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\ValueObject\Option;
use Symplify\SmartFileSystem\SmartFileSystem;

abstract class AbstractSymplifyCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    protected $symfonyStyle;

    /**
     * @var SmartFileSystem
     */
    protected $smartFileSystem;

    public function __construct()
    {
        parent::__construct();

        $this->addOption(Option::CONFIG, 'c', InputOption::VALUE_REQUIRED, 'Path to config file');
    }

    /**
     * @required
     */
    public function autowireAbstractSymplifyCommand(SymfonyStyle $symfonyStyle, SmartFileSystem $smartFileSystem): void
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->smartFileSystem = $smartFileSystem;
    }
}
