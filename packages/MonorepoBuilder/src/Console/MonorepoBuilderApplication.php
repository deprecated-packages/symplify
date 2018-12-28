<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symplify\PackageBuilder\Console\HelpfulApplicationTrait;

final class MonorepoBuilderApplication extends Application
{
    use HelpfulApplicationTrait;

    protected function getDefaultInputDefinition(): InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();

        $inputDefinition->addOption(new InputOption(
            'config',
            'c',
            InputOption::VALUE_REQUIRED,
            'Path to config file.',
            'monorepo-builder.(yml|yaml)'
        ));

        return $inputDefinition;
    }
}
