<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\Statie\Application\Command\RunCommand;
use Symplify\Statie\Application\SculpinApplication;
use Throwable;

final class GenerateCommand extends Command
{
    /**
     * @var SculpinApplication
     */
    private $sculpinApplication;

    public function __construct(SculpinApplication $sculpinApplication)
    {
        $this->sculpinApplication = $sculpinApplication;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('generate');
        $this->setDescription('Generate a site from source.');
        $this->addOption('server', null, InputOption::VALUE_NONE, 'Start local server to host your generated site.');

        $this->addOption(
            'source',
            null,
            InputOption::VALUE_REQUIRED,
            'Directory to load page FROM.',
            getcwd() . DIRECTORY_SEPARATOR . 'source'
        );
        $this->addOption(
            'output',
            null,
            InputOption::VALUE_REQUIRED,
            'Directory to generate page TO.',
            getcwd() . DIRECTORY_SEPARATOR . 'output'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $runCommand = new RunCommand(
                (bool) $input->getOption('server'),
                $input->getOption('source'),
                $input->getOption('output')
            );

            $this->sculpinApplication->runCommand($runCommand);

            $output->writeln('<info>Website was successfully generated.</info>');

            return 0;
        } catch (Throwable $throwable) {
            $output->writeln(
                sprintf('<error>%s</error>', $throwable->getMessage())
            );

            return 1;
        }
    }
}
