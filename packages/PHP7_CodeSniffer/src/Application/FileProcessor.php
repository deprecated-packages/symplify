<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Application;

use PHP_CodeSniffer\Files\File;
use Symplify\PHP7_CodeSniffer\EventDispatcher\Event\CheckFileTokenEvent;
use Symplify\PHP7_CodeSniffer\EventDispatcher\SniffDispatcher;

final class FileProcessor
{
    /**
     * @var SniffDispatcher
     */
    private $sniffDispatcher;

    /**
     * @var Fixer
     */
    private $fixer;

    public function __construct(SniffDispatcher $sniffDispatcher, Fixer $fixer)
    {
        $this->sniffDispatcher = $sniffDispatcher;
        $this->fixer = $fixer;
    }

    public function processFiles(array $files, bool $isFixer)
    {
        foreach ($files as $file) {
            $this->processFile($file, $isFixer);
        }
    }

    private function processFile(File $file, bool $isFixer)
    {
        if ($isFixer) {
            $this->processFileWithFixer($file);
        } else {
            $this->processFileWithoutFixer($file);
        }
    }

    private function processFileWithFixer(File $file)
    {
        // 1. puts tokens into fixer
        $file->fixer->startFile($file);

        // 2. run all Sniff fixers
        $this->processFileWithoutFixer($file);

        // 3. content has changed, save it!
        $newContent = $file->fixer->getContents();

        file_put_contents($file->getFilename(), $newContent);
    }

    private function processFileWithoutFixer(File $file)
    {
        foreach ($file->getTokens() as $stackPointer => $token) {
            $this->sniffDispatcher->dispatch(
                $token['code'],
                new CheckFileTokenEvent($file, $stackPointer)
            );
        }
    }
}
