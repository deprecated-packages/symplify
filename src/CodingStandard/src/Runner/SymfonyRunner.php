<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\CodingStandard\Runner;

use Symplify\CodingStandard\Contract\Runner\RunnerInterface;
use Symplify\CodingStandard\Process\PhpCsFixerProcessBuilder;

final class SymfonyRunner implements RunnerInterface
{
    /**
     * @var bool
     */
    private $hasErrors = false;

    /**
     * {@inheritdoc}
     */
    public function runForDirectory(string $directory) : string
    {
        $builder = new PhpCsFixerProcessBuilder($directory);
        $builder->setLevel('symfony');
        $builder->setFixers('-phpdoc_params');
        $builder->enableDryRun();

        $process = $builder->getProcess();
        $process->run();

        $this->detectErrorsInOutput($process->getOutput());

        return $process->getOutput();
    }

    /**
     * {@inheritdoc}
     */
    public function hasErrors() : bool
    {
        return $this->hasErrors;
    }

    /**
     * {@inheritdoc}
     */
    public function fixDirectory(string $directory) : string
    {
        $builder = new PhpCsFixerProcessBuilder($directory);
        $builder->setLevel('symfony');
        $builder->setFixers('-phpdoc_params');

        $process = $builder->getProcess();
        $process->run();

        return $process->getOutput();
    }

    private function detectErrorsInOutput(string $output)
    {
        if (strpos($output, 'end diff') !== false) {
            $this->hasErrors = true;
        }
    }
}
