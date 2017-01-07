<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\File\Provider;

use SplFileInfo;
use Symplify\PHP7_CodeSniffer\File\File;
use Symplify\PHP7_CodeSniffer\File\FileFactory;
use Symplify\PHP7_CodeSniffer\File\Finder\SourceFinder;

final class FilesProvider
{
    /**
     * @var SourceFinder
     */
    private $sourceFinder;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var File[][]
     */
    private $filesBySource = [];

    public function __construct(SourceFinder $sourceFinder, FileFactory $fileFactory)
    {
        $this->sourceFinder = $sourceFinder;
        $this->fileFactory = $fileFactory;
    }

    /**
     * @return File[]
     */
    public function getFilesForSource(array $source, bool $isFixer) : array
    {
        $sourceHash = md5(json_encode($source));
        if (isset($this->filesBySource[$sourceHash])) {
            return $this->filesBySource[$sourceHash];
        }

        return $this->filesBySource[$sourceHash] = $this->wrapFilesToValueObjects(
            $this->sourceFinder->find($source),
            $isFixer
        );
    }

    /**
     * @param SplFileInfo[] $files
     * @param bool $isFixer
     * @return File[]
     */
    private function wrapFilesToValueObjects(array $files, bool $isFixer) : array
    {
        foreach ($files as $name => $fileInfo) {
            $files[$name] = $this->fileFactory->create($name, $isFixer);
        }

        return $files;
    }
}
