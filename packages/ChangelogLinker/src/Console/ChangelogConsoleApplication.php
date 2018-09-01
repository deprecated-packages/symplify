<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

final class ChangelogConsoleApplication extends Application
{
    protected function getDefaultInputDefinition(): InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();

        // adds "file" argument
        $inputDefinition->addArgument(
            new InputArgument('file', InputArgument::OPTIONAL, 'Path to CHANGELOG.md', getcwd() . '/CHANGELOG.md')
        );

        // adds "--config" | "-c" option
        $inputDefinition->addOption(new InputOption('config', 'c', InputOption::VALUE_REQUIRED, 'Config file.'));

        return $inputDefinition;
    }
}
