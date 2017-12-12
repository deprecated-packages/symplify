<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\File;

use Symfony\Component\Finder\SplFileInfo;

final class FileFactory
{
    /**
     * @param SplFileInfo[] $fileInfos
     * @return AbstractFile[]
     */
    public function createFromFileInfos(array $fileInfos): array
    {
        $files = [];
        foreach ($fileInfos as $id => $fileInfo) {
            $files[$id] = $this->createFromFileInfo($fileInfo);
        }

        return $files;
    }

    /**
     * @return File|PostFile
     */
    public function createFromFileInfo(SplFileInfo $fileInfo): AbstractFile
    {
        return new File($fileInfo, $fileInfo->getRelativePathname(), $fileInfo->getPathname());
    }
}
