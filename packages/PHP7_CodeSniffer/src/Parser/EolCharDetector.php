<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Parser;

use Nette\Utils\FileSystem;

final class EolCharDetector
{
    public function detectForFilePath(string $filePath) : string
    {
        $content = FileSystem::read($filePath);
        return $this->detectForContent($content);
    }

    public function detectForContent(string $content) : string
    {
        if (preg_match("/\r\n?|\n/", $content, $matches) !== 1) {
            // Assume there are no newlines.
            return "\n";
        }

        return $matches[0];
    }
}
