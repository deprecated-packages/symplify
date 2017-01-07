<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\File\Finder;

use SplFileInfo;
use Symfony\Component\Finder\Finder;

final class SourceFinder
{
    /**
     * @param string[]
     * @return SplFileInfo[]
     */
    public function find(array $source) : array
    {
        $files = [];

        foreach ($source as $singleSource) {
            if (is_file($singleSource)) {
                $files = $this->processFile($files, $singleSource);
            } else {
                $files = $this->processDirectory($files, $singleSource);
            }
        }

        return $files;
    }

    private function processFile(array $files, string $file) : array
    {
        $fileInfo = new SplFileInfo($file);
        if ($fileInfo->getExtension() !== 'php') {
            return $files;
        }

        $files[$file] = $fileInfo;

        return $files;
    }

    private function processDirectory(array $files, string $directory) : array
    {
        $finder = (new Finder())->files()
            ->name('*.php')
            ->in($directory);

        return array_merge(
            $files,
            iterator_to_array($finder->getIterator())
        );
    }
}
