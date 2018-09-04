<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\ChangelogLinker\Configuration\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class ChangelogConsoleApplication extends Application
{
    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    /**
     * @required
     */
    public function setRequiredDependencies(ParameterProvider $parameterProvider): void
    {
        $this->parameterProvider = $parameterProvider;
    }

    protected function doRunCommand(Command $command, InputInterface $input, OutputInterface $output): int
    {
        // required to merge application + command definitions
        $command->mergeApplicationDefinition();

        $input->bind($command->getDefinition());

        $this->parameterProvider->changeParameter(Option::FILE, $input->getArgument(Option::FILE));

        return parent::doRunCommand($command, $input, $output);
    }

    protected function getDefaultInputDefinition(): InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();

        // adds "file" argument
        $inputDefinition->addArgument(
            new InputArgument(Option::FILE, InputArgument::OPTIONAL, 'Path to CHANGELOG.md', getcwd() . '/CHANGELOG.md')
        );

        // adds "--config" | "-c" option
        $inputDefinition->addOption(new InputOption('config', 'c', InputOption::VALUE_REQUIRED, 'Config file.'));

        return $inputDefinition;
    }
}
