<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Parser;

use Nette\Utils\FileSystem;
use PHP_CodeSniffer\Tokenizers\PHP;
use stdClass;

final class FileToTokensParser
{
    /**
     * @var EolCharDetector
     */
    private $eolCharDetector;

    public function __construct(EolCharDetector $eolCharDetector)
    {
        $this->eolCharDetector = $eolCharDetector;
    }

    /**
     * @var stdClass
     */
    private $legacyConfig;

    public function parseFromFilePath(string $filePath) : array
    {
        $fileContent = FileSystem::read($filePath);
        $eolChar = $this->eolCharDetector->detectForContent($fileContent);

        return $this->parseLegacyWithFileContentAndEolChar($fileContent, $eolChar);
    }

    private function parseLegacyWithFileContentAndEolChar(
        string $fileContent,
        string $eolChar
    ) : array {
        return (new PHP($fileContent, $this->getLegacyConfig(), $eolChar))->getTokens();
    }

    private function getLegacyConfig() : stdClass
    {
        if ($this->legacyConfig) {
            return $this->legacyConfig;
        }

        $config = new stdClass();
        $config->tabWidth = 4;
        $this->legacyConfig = $config;

        return $this->legacyConfig;
    }
}
