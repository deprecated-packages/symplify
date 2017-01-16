<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\MultiCodingStandard\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symplify\MultiCodingStandard\Console\Command\RunCommand;

final class ConsoleApplication extends Application
{
    public function __construct(RunCommand $runCommand)
    {
        parent::__construct('Symplify Coding Standard', null);
        $this->add($runCommand);
    }

    protected function getDefaultInputDefinition() : InputDefinition
    {
        return new InputDefinition([
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),
            new InputOption('--help', '-h', InputOption::VALUE_NONE, 'Display this help message'),
        ]);
    }
}
