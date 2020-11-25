<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\Console;

use Nette\Utils\Strings;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Descriptor\TextDescriptor;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

abstract class AbstractSymplifyConsoleApplication extends Application
{
    /**
     * @var string
     */
    private const COMMAND = 'command';

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

    protected function doRunCommand(Command $command, InputInterface $input, OutputInterface $output): int
    {
        return $this->doRunCommandAndShowHelpOnArgumentError($command, $input, $output);
    }

    protected function doRunCommandAndShowHelpOnArgumentError(
        Command $command,
        InputInterface $input,
        OutputInterface $output
    ): int {
        try {
            return parent::doRunCommand($command, $input, $output);
        } catch (RuntimeException $runtimeException) {
            if (Strings::contains($runtimeException->getMessage(), 'Provide required arguments')) {
                $this->cleanExtraCommandArgument($command);
                $textDescriptor = new TextDescriptor();
                $textDescriptor->describe($output, $command);

                return ShellCode::SUCCESS;
            }

            throw $runtimeException;
        }
    }

    /**
     * Sometimes there is "command" argument,
     * not really needed on fail of missing argument
     */
    private function cleanExtraCommandArgument(Command $command): void
    {
        $inputDefinition = $command->getDefinition();
        $arguments = $inputDefinition->getArguments();

        if (! isset($arguments[self::COMMAND])) {
            return;
        }

        unset($arguments[self::COMMAND]);

        $inputDefinition->setArguments($arguments);
    }
}
