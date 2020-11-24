<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\Finder\SmartFinder;
use Symplify\SmartFileSystem\SmartFileSystem;

abstract class AbstractMigrifyCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    protected $symfonyStyle;

    /**
     * @var SmartFinder
     */
    protected $smartFinder;

    /**
     * @var FileSystemGuard
     */
    protected $fileSystemGuard;

    /**
     * @var SmartFileSystem
     */
    protected $smartFileSystem;

    /**
     * @required
     */
    public function autowireAbstractSymplifyCommand(
        SmartFinder $smartFinder,
        SymfonyStyle $symfonyStyle,
        FileSystemGuard $fileSystemGuard,
        SmartFileSystem $smartFileSystem
    ): void {
        $this->smartFinder = $smartFinder;
        $this->symfonyStyle = $symfonyStyle;
        $this->fileSystemGuard = $fileSystemGuard;
        $this->smartFileSystem = $smartFileSystem;
    }
}
