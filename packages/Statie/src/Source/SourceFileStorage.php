<?php declare(strict_types=1);

namespace Symplify\Statie\Source;

use SplFileInfo;
use Symplify\Statie\Contract\Source\SourceFileFilter\SourceFileFilterInterface;

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

    /**
     * @param SplFileInfo[] $files
     */
    public function loadSourcesFromFiles(array $files)
    {
        foreach ($files as $fileInfo) {
            $this->addSource($fileInfo);
        }
    }

    private function addSource(SplFileInfo $fileInfo)
    {
        foreach ($this->sourceFileFilters as $sourceFileFilter) {
            if ($sourceFileFilter->matchesFileSource($fileInfo)) {
                $this->sourceFilesByType[$sourceFileFilter->getName()][$fileInfo->getRealPath()] = $fileInfo;
            }
        }
    }

    /**
     * @return SplFileInfo[]
     */
    public function getStaticFiles() : array
    {
        ksort($this->sourceFilesByType[SourceFileTypes::STATIC]);

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
        krsort($this->sourceFilesByType[SourceFileTypes::POSTS]);

        return $this->sourceFilesByType[SourceFileTypes::POSTS];
    }

    /**
     * @return SplFileInfo[]
     */
    public function getLayoutFiles() : array
    {
        return $this->sourceFilesByType[SourceFileTypes::GLOBAL_LATTE];
    }
}
