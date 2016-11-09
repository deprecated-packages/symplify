<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\CodingStandard\Command;

use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('check');
        $this->setDescription('Check coding standard in particular directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            foreach ($input->getArgument('path') as $path) {
                $this->executeRunnersForDirectory($path);
            }

            return $this->outputCheckResult();
        } catch (Exception $exception) {
            $this->io->error($exception->getMessage());

            return self::EXIT_CODE_ERROR;
        }
    }

    private function executeRunnersForDirectory(string $directory)
    {
        foreach ($this->runnerCollection->getRunners() as $runner) {
            $headline = 'Running ' . $this->getRunnerName($runner) . ' in ' . $directory;
            $this->io->section($headline);

            $processOutput = $runner->runForDirectory($directory);
            $this->io->text($processOutput);

            if ($runner->hasErrors()) {
                $this->exitCode = self::EXIT_CODE_ERROR;
            }
        }
    }

    private function outputCheckResult(): int
    {
        if ($this->exitCode === self::EXIT_CODE_ERROR) {
            $this->io->error('Some errors were found');
        } else {
            $this->io->success('Check was finished with no errors!');
        }

        return $this->exitCode;
    }
}
