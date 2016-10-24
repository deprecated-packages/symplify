<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Renderable\File;

use Nette\Utils\Strings;
use SplFileInfo;
use Symplify\PHP7_Sculpin\Configuration\Configuration;

final class FileFactory
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return File|PostFile
     */
    public function create(SplFileInfo $file) : File
    {
        $relativeSource = substr($file->getPathname(), strlen($this->configuration->getSourceDirectory()));
        $relativeSource = ltrim($relativeSource, DIRECTORY_SEPARATOR);

        if (Strings::endsWith($file->getPath(), '_posts')) {
            return new PostFile($file, $relativeSource);
        }

        return new File($file, $relativeSource);
    }
}
