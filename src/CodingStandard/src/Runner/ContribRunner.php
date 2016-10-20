<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\CodingStandard\Runner;

use Symplify\CodingStandard\Contract\Runner\RunnerInterface;
use Symplify\CodingStandard\Process\PhpCsFixerProcessBuilder;

final class ContribRunner implements RunnerInterface
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
        $builder->setFixers($this->getCustomFixers());
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
        $builder->setFixers($this->getCustomFixers());

        $process = $builder->getProcess();
        $process->run();

        return $process->getOutput();
    }

    /**
     * @param string $output
     */
    private function detectErrorsInOutput($output)
    {
        if (strpos($output, 'end diff') !== false) {
            $this->hasErrors = true;
        }
    }

    private function getCustomFixers() : string
    {
        $fixers = [
            'short_array_syntax',
            'newline_after_open_tag',
            'ordered_use',
            'php_unit_construct',
            'phpdoc_order',
            'strict',
        ];

        return implode(',', $fixers);
    }
}
