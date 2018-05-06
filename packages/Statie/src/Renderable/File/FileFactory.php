<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\File;

use Symfony\Component\Finder\SplFileInfo;
use Symplify\Statie\Utils\PathAnalyzer;

final class FileFactory
{
    /**
     * @var PathAnalyzer
     */
    private $pathAnalyzer;

    public function __construct(PathAnalyzer $pathAnalyzer)
    {
        $this->pathAnalyzer = $pathAnalyzer;
    }

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

    public function createFromFileInfo(SplFileInfo $fileInfo): AbstractFile
    {
        return new File(
            $fileInfo,
            $fileInfo->getRelativePathname(),
            $fileInfo->getPathname(),
            $this->pathAnalyzer->detectFilenameWithoutDate($fileInfo),
            $this->pathAnalyzer->detectDate($fileInfo)
        );
    }
}
