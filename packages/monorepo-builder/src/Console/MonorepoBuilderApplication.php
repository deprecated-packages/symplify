<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\Console\Command\BumpInterdependencyCommand;
use Symplify\MonorepoBuilder\Console\Command\ValidateCommand;
use Symplify\MonorepoBuilder\Merge\Command\MergeCommand;
use Symplify\MonorepoBuilder\Release\Command\ReleaseCommand;
use Symplify\MonorepoBuilder\Validator\SourcesPresenceValidator;
use Symplify\SymplifyKernel\Console\AbstractSymplifyConsoleApplication;

final class MonorepoBuilderApplication extends AbstractSymplifyConsoleApplication
{
    /**
     * @var SourcesPresenceValidator
     */
    private $sourcesPresenceValidator;

    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands, SourcesPresenceValidator $sourcesPresenceValidator)
    {
        $this->addCommands($commands);
        $this->sourcesPresenceValidator = $sourcesPresenceValidator;

        parent::__construct();
    }

    protected function getDefaultInputDefinition(): InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();

        $inputDefinition->addOption(new InputOption(
            'config',
            'c',
            InputOption::VALUE_REQUIRED,
            'Path to config file.',
            'monorepo-builder.php'
        ));

        return $inputDefinition;
    }

    protected function doRunCommand(Command $command, InputInterface $input, OutputInterface $output): int
    {
        $this->validateSources($command);

        return $this->doRunCommandAndShowHelpOnArgumentError($command, $input, $output);
    }

    private function validateSources(Command $command): void
    {
        $commandClass = get_class($command);

        if (in_array($commandClass, [ValidateCommand::class, MergeCommand::class], true)) {
            $this->sourcesPresenceValidator->validatePackageComposerJsons();
        }

        if (in_array($commandClass, [BumpInterdependencyCommand::class, ReleaseCommand::class], true)) {
            $this->sourcesPresenceValidator->validateRootComposerJsonName();
        }
    }
}
