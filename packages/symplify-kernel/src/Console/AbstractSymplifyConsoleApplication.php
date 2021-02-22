<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

abstract class AbstractSymplifyConsoleApplication extends Application
{
    /**
     * @var CommandNaming
     */
    private $commandNaming;

    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands, string $name = 'UNKNOWN', string $version = 'UNKNOWN')
    {
        $this->commandNaming = new CommandNaming();

        $this->addCommands($commands);

        parent::__construct($name, $version);
    }

    /**
     * Add names to all commands by class-name convention
     * @param Command[] $commands
     */
    public function addCommands(array $commands): void
    {
        foreach ($commands as $command) {
            $commandName = $this->commandNaming->resolveFromCommand($command);
            $command->setName($commandName);
        }

        parent::addCommands($commands);
    }
}
