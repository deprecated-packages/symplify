<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\CodingStandard\Runner;

use Symfony\CS\Fixer\Contrib\HeaderCommentFixer;
use Symplify\CodingStandard\Contract\Runner\RunnerInterface;
use Symplify\CodingStandard\Process\PhpCsFixerProcessBuilder;

final class ContribRunner implements RunnerInterface
{
    /**
     * @var bool
     */
    private $hasErrors = false;

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

    public function hasErrors() : bool
    {
        return $this->hasErrors;
    }

    public function fixDirectory(string $directory) : string
    {
        $builder = new PhpCsFixerProcessBuilder($directory);
        $builder->setFixers($this->getCustomFixers());

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

    /**
     * See here a bit bellow for all custom fixers:
     * https://github.com/FriendsOfPHP/PHP-CS-Fixer#usage
     */
    private function getCustomFixers() : string
    {
        $fixers = [
            'combine_consecutive_unsets',
            'concat_with_spaces',
            'empty_return',
            'short_array_syntax',
            // 'header_comment', : @see setupFixers() method bellow
            'logical_not_operators_with_successor_space',
            'newline_after_open_tag',
            'no_useless_else',
            'no_useless_return',
            'ordered_use',
            'php_unit_construct',
            'php_unit_dedicate_assert',
            'php_unit_strict',
            'phpdoc_order',
            'short_echo_tag',
            'strict',
        ];

        return implode(',', $fixers);
    }

    /**
     * Todo: add later when refactoring to PHP use and own process.
     * http://stackoverflow.com/questions/35121798/how-to-configure-headercommentfixer-in-php-cs-fixer
     */
    private function setupFixers()
    {
        HeaderCommentFixer::setHeader(
            file_get_contents(__DIR__ . '/PhpCsFixer/desired-header.txt')
        );
    }
}
