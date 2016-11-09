<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\Renderable\File;

use SplFileInfo;

abstract class AbstractFile
{
    /**
     * @var SplFileInfo
     */
    protected $fileInfo;

    /**
     * @var array
     */
    protected $configuration = [];

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

    public function getOutputPath() : string
    {
        return $this->outputPath;
    }

    public function setRelativeUrl(string $relativeUrl)
    {
        $this->configuration['relativeUrl'] = $relativeUrl;
    }

    public function getRelativeUrl() : string
    {
        return $this->configuration['relativeUrl'];
    }

    public function getRelativeSource() : string
    {
        return $this->relativeSource;
    }

    public function getBaseName() : string
    {
        return $this->fileInfo->getBasename('.' . $this->fileInfo->getExtension());
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
        $this->configuration += $configuration;
    }

    public function getConfiguration() : array
    {
        return $this->configuration;
    }

    public function getLayout() : string
    {
        return $this->configuration['layout'] ?? '';
    }
}
