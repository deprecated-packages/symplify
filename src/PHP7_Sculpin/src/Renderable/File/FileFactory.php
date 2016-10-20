<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Renderable\File;

use Nette\Utils\Strings;
use SplFileInfo;

final class FileFactory
{
    /**
     * @var string
     */
    private $sourceDirectory;

    public function __construct(string $sourceDirectory)
    {
        $this->sourceDirectory = $sourceDirectory;
    }

    /**
     * @return File|PostFile
     */
    public function create(SplFileInfo $file) : File
    {
        $relativeSource = substr($file->getPathname(), strlen($this->sourceDirectory));
        $relativeSource = ltrim($relativeSource, DIRECTORY_SEPARATOR);
        if (Strings::endsWith($file->getPath(), '_posts')) {
            return new PostFile($file, $relativeSource);
        }

        return new File($file, $relativeSource);
    }
}
