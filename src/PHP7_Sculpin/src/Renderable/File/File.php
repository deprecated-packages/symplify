<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Renderable\File;

use SplFileInfo;

class File
{
    /**
     * @var SplFileInfo
     */
    protected $fileInfo;

    /**
     * @var string
     */
    private $relativeSource;

    /**
     * @var string
     */
    private $outputPath;

    /**
     * @var string
     */
    private $content;

    /**
     * @var array
     */
    private $configuration = [];

    public function __construct(SplFileInfo $fileInfo, string $relativeSource)
    {
        $this->relativeSource = $relativeSource;
        $this->fileInfo = $fileInfo;
        $this->content = file_get_contents($fileInfo->getRealPath());
    }

    public function setOutputPath(string $outputPath)
    {
        $this->outputPath = $outputPath;
    }

    public function getRelativeSource() : string
    {
        return $this->relativeSource;
    }

    public function getBaseName() : string
    {
        return $this->fileInfo->getBasename('.'.$this->fileInfo->getExtension());
    }

    public function getPrimaryExtension() : string
    {
        $fileParts = explode('.', $this->fileInfo->getBasename());
        if (count($fileParts) > 2) {
            return $fileParts[count($fileParts) - 2];
        }

        return $fileParts[count($fileParts) - 1];
    }

    public function getExtension() : string
    {
        return $this->fileInfo->getExtension();
    }

    public function getRelativeUrl() : string
    {
        if ($position = strpos($this->outputPath, DIRECTORY_SEPARATOR.'index.html')) {
            $directoryPath = substr($this->outputPath, 0, $position);

            return str_replace('\\', '/', $directoryPath);
        }

        return $this->outputPath;
    }

    public function getContent() : string
    {
        return $this->content;
    }

    public function changeContent(string $newContent)
    {
        $this->content = $newContent;
    }

    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration() : array
    {
        return $this->configuration;
    }

    public function getOutputPath() : string
    {
        return $this->outputPath;
    }

    public function getLayout() : string
    {
        return $this->configuration['layout'] ?? '';
    }
}
