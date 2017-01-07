<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Report;

use Symplify\PHP7_CodeSniffer\EventDispatcher\CurrentListenerSniffCodeProvider;
use Symplify\PHP7_CodeSniffer\File\File;

final class ErrorDataCollector
{
    /**
     * @var int
     */
    private $errorCount = 0;

    /**
     * @var int
     */
    private $fixableErrorCount = 0;

    /**
     * @var array[]
     */
    private $errorMessages = [];

    /**
     * @var CurrentListenerSniffCodeProvider
     */
    private $currentListenerSniffCodeProvider;

    /**
     * @var ErrorMessageSorter
     */
    private $errorMessageSorter;

    public function __construct(
        CurrentListenerSniffCodeProvider $currentListenerSniffCodeProvider,
        ErrorMessageSorter $errorMessageSorter
    ) {
        $this->currentListenerSniffCodeProvider = $currentListenerSniffCodeProvider;
        $this->errorMessageSorter = $errorMessageSorter;
    }

    public function getErrorCount() : int
    {
        return $this->errorCount;
    }

    public function getFixableErrorCount() : int
    {
        return $this->fixableErrorCount;
    }

    public function getUnfixableErrorCount() : int
    {
        return $this->errorCount - $this->fixableErrorCount;
    }

    public function getErrorMessages() : array
    {
        return $this->errorMessageSorter->sortByFileAndLine($this->errorMessages);
    }

    public function getUnfixableErrorMessages() : array
    {
        $unfixableErrorMessages = [];
        foreach ($this->getErrorMessages() as $file => $errorMessagesForFile) {
            $unfixableErrorMessagesForFile = $this->filterUnfixableErrorMessagesForFile($errorMessagesForFile);
            if (count($unfixableErrorMessagesForFile)) {
                $unfixableErrorMessages[$file] = $unfixableErrorMessagesForFile;
            }
        }

        return $unfixableErrorMessages;
    }

    public function addErrorMessage(
        string $filePath,
        string $message,
        int $line,
        string $sniffCode,
        array $data = [],
        bool $isFixable = false
    ) {
        $this->errorCount++;

        if ($isFixable) {
            $this->fixableErrorCount++;
        }

        $this->errorMessages[$filePath][] = [
            'line' => $line,
            'message' => $this->applyDataToMessage($message, $data),
            'sniffCode' => $this->getSniffFullCode($sniffCode),
            'isFixable'  => $isFixable
        ];
    }

    private function getSniffFullCode(string $sniffCode) : string
    {
        $parts = explode('.', $sniffCode);
        if ($parts[0] !== $sniffCode) {
            return $sniffCode;
        }

        $listenerSniffCode = $this->currentListenerSniffCodeProvider->getCurrentListenerSniffCode();
        return $listenerSniffCode.'.'.$sniffCode;
    }

    private function applyDataToMessage(string $message, array $data) : string
    {
        if (count($data)) {
            $message = vsprintf($message, $data);
        }

        return $message;
    }

    private function filterUnfixableErrorMessagesForFile(array $errorMessagesForFile) : array
    {
        $unfixableErrorMessages = [];
        foreach ($errorMessagesForFile as $errorMessage) {
            if ($errorMessage['isFixable']) {
                continue;
            }

            $unfixableErrorMessages[] = $errorMessage;
        }

        return $unfixableErrorMessages;
    }
}
