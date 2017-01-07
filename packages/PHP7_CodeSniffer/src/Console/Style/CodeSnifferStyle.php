<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Console\Style;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CodeSnifferStyle
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->symfonyStyle = new SymfonyStyle($input, $output);
    }

    public function success(string $message)
    {
        $this->symfonyStyle->success($message);
    }

    public function error(string $message)
    {
        $this->symfonyStyle->error($message);
    }

    public function writeErrorReports(array $errorReports)
    {
        foreach ($errorReports as $file => $errors) {
            $this->symfonyStyle->section('FILE: ' . $file);

            $tableRows = $this->formatErrorsToTableRows($errors);
            $this->symfonyStyle->table(['Line', 'Error', 'Sniff Code', 'Fixable'], $tableRows);

            $this->symfonyStyle->newLine();
        }
    }

    private function formatErrorsToTableRows(array $errors) : array
    {
        foreach ($errors as $key => $error) {
            $errors[$key]['isFixable'] = $error['isFixable'] ? 'YES' : 'No';
        }

        return $errors;
    }
}
