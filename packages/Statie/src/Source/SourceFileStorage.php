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

    public function addSourceFileFilter(SourceFileFilterInterface $sourceFileFilter): void
    {
        $this->sourceFileFilters[$sourceFileFilter->getName()] = $sourceFileFilter;
        $this->sourceFilesByType[$sourceFileFilter->getName()] = [];
    }

    /**
     * @param SplFileInfo[] $files
     */
    public function loadSourcesFromFiles(array $files): void
    {
        foreach ($files as $fileInfo) {
            $this->addSource($fileInfo);
        }
    }

    /**
     * @return SplFileInfo[]
     */
    public function getRenderableFiles(): array
    {
        return $this->sourceFilesByType[SourceFileTypes::RENDERABLE];
    }

    private function addSource(SplFileInfo $fileInfo): void
    {
        foreach ($this->sourceFileFilters as $sourceFileFilter) {
            if ($sourceFileFilter->matchesFileSource($fileInfo)) {
                $this->sourceFilesByType[$sourceFileFilter->getName()][$fileInfo->getRealPath()] = $fileInfo;
            }
        }
    }
}
