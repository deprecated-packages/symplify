<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Runner;

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
        $builder->setRules($this->getCustomFixers());
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
        $builder->setRules($this->getCustomFixers());

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
     * https://github.com/FriendsOfPHP/PHP-CS-Fixer#usage.
     */
    private function getCustomFixers() : string
    {
        $fixers = [
            // since 2.0
            'psr4',
            'phpdoc_no_alias_tag',
            // 'ordered_class_elements', requires PHP configuration
            'no_spaces_around_offset',
            'dir_constant',
            'modernize_types_casting',
            'random_api_migration',
            'single_class_element_per_statement',
            'declare_strict_types',
            'normalize_index_brace',
            'semicolon_after_instruction',
            // since 1.x
            'combine_consecutive_unsets',
            // 'concat_space', requires PHP configuration
            'simplified_null_return',
            // 'array_syntax', requires PHP configuration
            'not_operator_with_successor_space',
            'linebreak_after_opening_tag',
            'no_useless_else',
            'no_useless_return',
            'ordered_imports',
            'php_unit_construct',
            'php_unit_dedicate_assert',
            'php_unit_strict',
            'no_short_echo_tag',
            'strict_comparison',
        ];

        return implode(',', $fixers);
    }
}
