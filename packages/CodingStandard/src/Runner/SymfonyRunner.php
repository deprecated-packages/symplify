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

    public function runForDirectory(string $directory) : string
    {
        $builder = $this->createBuilderWithDirectory($directory);
        $builder->enableDryRun();

        $process = $builder->getProcess();
        $process->run();

        $this->detectErrorsInOutput($process->getOutput());

        return $process->getOutput();
    }

    public function hasErrors() : bool
    {
        return $this->hasErrors;
    }

    public function fixDirectory(string $directory) : string
    {
        $builder = $this->createBuilderWithDirectory($directory);

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

    private function createBuilderWithDirectory(string $directory): PhpCsFixerProcessBuilder
    {
        $builder = new PhpCsFixerProcessBuilder($directory);
        $builder->setLevel('symfony');
        $builder->setFixers($this->getExcludedFixers());

        return $builder;
    }

    /**
     * See here a bit bellow for all custom fixers:
     * https://github.com/FriendsOfPHP/PHP-CS-Fixer#usage.
     */
    private function getExcludedFixers() : string
    {
        $fixers = [
            '-phpdoc_params',
            '-concat_without_spaces',
            '-unary_operators_spaces',
        ];

        return implode(',', $fixers);
    }
}
