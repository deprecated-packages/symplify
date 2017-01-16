<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\MultiCodingStandard\Console\Output;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Output\Output;
use Symplify\MultiCodingStandard\PhpCsFixer\Report\DiffDataCollector;
use Symplify\PHP7_CodeSniffer\Console\Output\InfoMessagePrinter as PhpCodeSnifferInfoMessagePrinter;
use Symplify\PHP7_CodeSniffer\Report\ErrorDataCollector;

final class InfoMessagePrinter
{
    /**
     * @var ErrorDataCollector
     */
    private $errorDataCollector;

    /**
     * @var DiffDataCollector
     */
    private $diffDataCollector;

    /**
     * @var PhpCodeSnifferInfoMessagePrinter
     */
    private $phpCodeSnifferInfoMessagePrinter;

    /**
     * @var Output
     */
    private $output;

    public function __construct(
        ErrorDataCollector $errorDataCollector,
        DiffDataCollector $diffDataCollector,
        PhpCodeSnifferInfoMessagePrinter $phpCodeSnifferInfoMessagePrinter,
        Output $output
    ) {
        $this->errorDataCollector = $errorDataCollector;
        $this->diffDataCollector = $diffDataCollector;
        $this->phpCodeSnifferInfoMessagePrinter = $phpCodeSnifferInfoMessagePrinter;
        $this->output = $output;
    }

    public function hasSomeErrorMessages() : bool
    {
        if ($this->errorDataCollector->getErrorCount()) {
            return true;
        }

        if (count($this->diffDataCollector->getDiffs())) {
            return true;
        }

        return false;
    }

    public function printFoundErrorsStatus(bool $isFixer)
    {
        // code sniffer
        $this->phpCodeSnifferInfoMessagePrinter->printFoundErrorsStatus($isFixer);

        // php-cs-fixer
        $diffs = $this->diffDataCollector->getDiffs();
        $this->printDiffs($diffs);
    }

    /**
     * Used original code from PHP-CS-FIXER/CS/Console/FixCommand.php
     * https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/185c1758aadc942956e84accf1b24be9e2609718/Symfony/CS/Console/Command/FixCommand.php#L431-L493.
     */
    private function printDiffs(array $diffs)
    {
        $fixerDetailLine = ' (<comment>%s</comment>)';

        $i = 0;
        foreach ($diffs as $file => $fixResult) {
            $this->output->write(sprintf('%4d) %s', $i++, $file));

            if ($fixerDetailLine) {
                $this->output->write(sprintf($fixerDetailLine, implode(', ', $fixResult['appliedFixers'])));
            }

            $this->output->writeln('');
            $this->output->writeln('<comment>      ---------- begin diff ----------</comment>');

            $diff = implode(
                PHP_EOL,
                array_map(
                    function ($string) {
                        $string = preg_replace('/^(\+){3}/', '<info>+++</info>', $string);
                        $string = preg_replace('/^(\+){1}/', '<info>+</info>', $string);
                        $string = preg_replace('/^(\-){3}/', '<error>---</error>', $string);
                        $string = preg_replace('/^(\-){1}/', '<error>-</error>', $string);
                        $string = str_repeat(' ', 6).$string;

                        return $string;
                    },
                    explode(PHP_EOL, OutputFormatter::escape($fixResult['diff']))
                )
            );

            $this->output->writeln($diff);

            $this->output->writeln('<comment>      ---------- end diff ----------</comment>');
        }

        $this->output->writeln('');
    }
}
