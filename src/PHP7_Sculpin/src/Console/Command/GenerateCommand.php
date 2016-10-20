<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PHP7_Sculpin\Application\Command\RunCommand;
use Symplify\PHP7_Sculpin\Application\SculpinApplication;
use Throwable;

final class GenerateCommand extends Command
{
    /**
     * @var string
     */
    private $sourceDirectory;

    /**
     * @var string
     */
    private $outputDirectory;

    /**
     * @var SculpinApplication
     */
    private $sculpinApplication;

    public function __construct(
        string $sourceDirectory,
        string $outputDirectory,
        SculpinApplication $sculpinApplication
    ) {
        $this->sourceDirectory = $sourceDirectory;
        $this->outputDirectory = $outputDirectory;
        $this->sculpinApplication = $sculpinApplication;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('generate');
        $this->setDescription('Generate a site from source.');
        $this->addOption('server', null, InputOption::VALUE_NONE, 'Start local server to host your generated site.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $runCommand = new RunCommand(
                (bool) $input->getOption('server'),
                $this->sourceDirectory,
                $this->outputDirectory
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
