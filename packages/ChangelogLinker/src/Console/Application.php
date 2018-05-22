<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputOption;

final class Application extends SymfonyApplication
{
    protected function getDefaultInputDefinition()
    {
        $inputDefinition = parent::getDefaultInputDefinition();

        // adds "--config" option
        $inputDefinition->addOption(new InputOption('config', null, InputOption::VALUE_REQUIRED, 'Config file.'));

        return $inputDefinition;
    }
}
