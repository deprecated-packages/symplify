<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

final class ConfigAwareApplication extends Application
{
    protected function getDefaultInputDefinition(): InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();

        // adds "--config" | "-c" option
        $inputDefinition->addOption(new InputOption('config', 'c', InputOption::VALUE_REQUIRED, 'Config file.'));

        return $inputDefinition;
    }
}
