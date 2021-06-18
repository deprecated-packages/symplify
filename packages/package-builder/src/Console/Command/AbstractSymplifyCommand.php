<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Service\Attribute\Required;
use Symplify\PackageBuilder\ValueObject\Option;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\Finder\SmartFinder;
use Symplify\SmartFileSystem\SmartFileSystem;

abstract class AbstractSymplifyCommand extends Command
{
    protected SymfonyStyle $symfonyStyle;

    protected SmartFileSystem $smartFileSystem;

    protected SmartFinder $smartFinder;

    protected FileSystemGuard $fileSystemGuard;

    public function __construct()
    {
        parent::__construct();

        $this->addOption(Option::CONFIG, 'c', InputOption::VALUE_REQUIRED, 'Path to config file');
    }

    #[Required]
    public function autowireAbstractSymplifyCommand(
        SymfonyStyle $symfonyStyle,
        SmartFileSystem $smartFileSystem,
        SmartFinder $smartFinder,
        FileSystemGuard $fileSystemGuard
    ): void {
        $this->symfonyStyle = $symfonyStyle;
        $this->smartFileSystem = $smartFileSystem;
        $this->smartFinder = $smartFinder;
        $this->fileSystemGuard = $fileSystemGuard;
    }
}
