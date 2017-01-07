<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Console\Output;

use Symplify\PHP7_CodeSniffer\Console\Style\CodeSnifferStyle;
use Symplify\PHP7_CodeSniffer\Report\ErrorDataCollector;

final class InfoMessagePrinter
{
    /**
     * @var CodeSnifferStyle
     */
    private $codeSnifferStyle;

    /**
     * @var ErrorDataCollector
     */
    private $errorDataCollector;

    public function __construct(
        CodeSnifferStyle $codeSnifferStyle,
        ErrorDataCollector $errorDataCollector
    ) {
        $this->codeSnifferStyle = $codeSnifferStyle;
        $this->errorDataCollector = $errorDataCollector;
    }

    public function printFoundErrorsStatus(bool $isFixer)
    {
        if ($isFixer) {
            $this->printUnfixedErrors();
        } else {
            $this->printErrors();
            $this->printFixingNote();
        }
    }

    private function printUnfixedErrors()
    {
        $this->codeSnifferStyle->writeErrorReports(
            $this->errorDataCollector->getUnfixableErrorMessages()
        );

        if ($this->errorDataCollector->getFixableErrorCount()) {
            $this->codeSnifferStyle->success(sprintf(
                'Congrats! %d error(s) were fixed.',
                $this->errorDataCollector->getFixableErrorCount()
            ));
        }

        if ($this->errorDataCollector->getUnfixableErrorCount()) {
            $this->codeSnifferStyle->error(sprintf(
                '%d error(s) could not be fixed. You have to do it manually.',
                $this->errorDataCollector->getUnfixableErrorCount()
            ));
        }
    }

    private function printErrors()
    {
        $this->codeSnifferStyle->writeErrorReports($this->errorDataCollector->getErrorMessages());
        $this->codeSnifferStyle->error(sprintf(
            '%d error(s) found.',
            $this->errorDataCollector->getErrorCount()
        ));
    }

    private function printFixingNote()
    {
        if ($fixableCount = $this->errorDataCollector->getFixableErrorCount()) {
            $howMany = $fixableCount;
            if ($fixableCount === $this->errorDataCollector->getErrorCount()) {
                $howMany = 'ALL';
            }

            $this->codeSnifferStyle->success(sprintf(
                'Good news is, we can fix %s of them for you. Just add "--fix".',
                $howMany
            ));
        }
    }
}
