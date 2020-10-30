<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console;

use Rector\Core\Configuration\Option as RectorOption;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\ChangelogLinker\Configuration\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SymplifyKernel\Console\AbstractSymplifyConsoleApplication;

final class ChangelogApplication extends AbstractSymplifyConsoleApplication
{
    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    /**
     * @param Command[] $commands
     */
    public function __construct(ParameterProvider $parameterProvider, array $commands)
    {
        $this->parameterProvider = $parameterProvider;
        $this->addCommands($commands);

        parent::__construct();
    }

    protected function doRunCommand(Command $command, InputInterface $input, OutputInterface $output): int
    {
        // required to merge application + command definitions
        $command->mergeApplicationDefinition();

        $input->bind($command->getDefinition());

        $this->parameterProvider->changeParameter(Option::FILE, $input->getArgument(Option::FILE));

        return $this->doRunCommandAndShowHelpOnArgumentError($command, $input, $output);
    }

    protected function getDefaultInputDefinition(): InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();

        // adds "file" argument
        $inputDefinition->addArgument(
            new InputArgument(Option::FILE, InputArgument::OPTIONAL, 'Path to CHANGELOG.md', getcwd() . '/CHANGELOG.md')
        );

        // adds "--config" | "-c" option
        $inputDefinition->addOption(
            new InputOption(RectorOption::OPTION_CONFIG, 'c', InputOption::VALUE_REQUIRED, 'Config file')
        );

        return $inputDefinition;
    }
}
