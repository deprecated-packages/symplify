<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

final class Application extends SymfonyApplication
{
    protected function getDefaultInputDefinition(): InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();

        // adds "--config" | "-c" option
        $inputDefinition->addOption(new InputOption('config', 'c', InputOption::VALUE_REQUIRED, 'Config file.'));

        return $inputDefinition;
    }
}
