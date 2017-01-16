<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\MultiCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symplify\MultiCodingStandard\Application\Application;
use Symplify\MultiCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\MultiCodingStandard\Configuration\MultiCsFileLoader;
use Symplify\MultiCodingStandard\Console\Output\InfoMessagePrinter;
use Symplify\PHP7_CodeSniffer\Console\ExitCode;
use Throwable;

final class RunCommand extends Command
{
    /**
     * @var StyleInterface
     */
    private $style;

    /**
     * @var Application
     */
    private $application;

    /**
     * @var MultiCsFileLoader
     */
    private $multiCsFileLoader;

    /**
     * @var InfoMessagePrinter
     */
    private $infoMessagePrinter;

    public function __construct(
        Application $application,
        StyleInterface $style,
        MultiCsFileLoader $multiCsFileLoader,
        InfoMessagePrinter $infoMessagePrinter
    ) {
        parent::__construct();

        $this->application = $application;
        $this->style = $style;
        $this->multiCsFileLoader = $multiCsFileLoader;
        $this->infoMessagePrinter = $infoMessagePrinter;
    }

    protected function configure()
    {
        $this->setName('run');
        $this->addArgument('source', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'The path(s) to be checked.');
        $this->addOption('fix', null, null, 'Fix found violations.');
        $this->setDescription('Check coding standard in one or more directories.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->application->runCommand(
                $this->createRunApplicationCommandFromInput($input)
            );

            if ($this->infoMessagePrinter->hasSomeErrorMessages()) {
                $this->infoMessagePrinter->printFoundErrorsStatus($input->getOption('fix'));

                return ExitCode::ERROR;
            }

            $this->style->success(
                sprintf(
                    'Sources "%s" were checked!',
                    implode(',', $input->getArgument('source'))
                )
            );

            return ExitCode::SUCCESS;
        } catch (Throwable $throwable) {
            if ($throwable->getMessage()) {
                $this->style->error($throwable->getMessage());
            }

            return ExitCode::ERROR;
        }
    }

    private function createRunApplicationCommandFromInput(InputInterface $input) : RunApplicationCommand
    {
        return new RunApplicationCommand(
            $input->getArgument('source'),
            $input->getOption('fix'),
            $this->multiCsFileLoader->load()
        );
    }
}
