<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Source;

use Nette\Utils\Finder;
use SplFileInfo;
use Symplify\PHP7_Sculpin\Contract\Source\SourceFileFilter\SourceFileFilterInterface;

final class SourceFileStorage
{
    /**
     * @var SplFileInfo[][]
     */
    private $sourceFilesByType = [];

    /**
     * @var SourceFileFilterInterface[]
     */
    private $sourceFileFilters = [];

    public function addSourceFileFilter(SourceFileFilterInterface $sourceFileFilter)
    {
        $this->sourceFileFilters[$sourceFileFilter->getName()] = $sourceFileFilter;
        $this->sourceFilesByType[$sourceFileFilter->getName()] = [];
    }

    public function loadSourcesFromFinder(Finder $finder)
    {
        foreach ($finder as $fileInfo) {
            $this->addSource($fileInfo);
        }
    }

    private function addSource(SplFileInfo $fileInfo)
    {
        foreach ($this->sourceFileFilters as $sourceFileFilter) {
            if ($sourceFileFilter->matchesFileSource($fileInfo)) {
                $this->sourceFilesByType[$sourceFileFilter->getName()][] = $fileInfo;
            }
        }
    }

    /**
     * @return SplFileInfo[]
     */
    public function getStaticFiles() : array
    {
        return $this->sourceFilesByType[SourceFileTypes::STATIC];
    }

    /**
     * @return SplFileInfo[]
     */
    public function getRenderableFiles() : array
    {
        return $this->sourceFilesByType[SourceFileTypes::RENDERABLE];
    }

    /**
     * @return SplFileInfo[]
     */
    public function getConfigurationFiles() : array
    {
        return $this->sourceFilesByType[SourceFileTypes::CONFIGURATION];
    }

    /**
     * @return SplFileInfo[]
     */
    public function getPostFiles() : array
    {
        return $this->sourceFilesByType[SourceFileTypes::POSTS];
    }

    /**
     * @return SplFileInfo[]
     */
    public function getLayoutFiles() : array
    {
        return $this->sourceFilesByType[SourceFileTypes::LAYOUTS];
    }
}
