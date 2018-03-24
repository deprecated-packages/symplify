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

    /**
     * @param SplFileInfo[] $fileInfos
     * @return AbstractFile[]
     */
    public function createFromFileInfosAndClass(array $fileInfos, string $class): array
    {
        $objects = [];

        foreach ($fileInfos as $fileInfo) {
            $objects[] = $this->createFromClassNameAndFileInfo($class, $fileInfo);
        }

        return $objects;
    }

    public function createFromFileInfo(SplFileInfo $fileInfo): File
    {
        return $this->createFromClassNameAndFileInfo(File::class, $fileInfo);
    }

    private function createFromClassNameAndFileInfo(string $className, SplFileInfo $fileInfo): AbstractFile
    {
        $dateTime = $this->pathAnalyzer->detectDate($fileInfo);
        if ($dateTime) {
            $filenameWithoutDate = $this->pathAnalyzer->detectFilenameWithoutDate($fileInfo);
        } else {
            $filenameWithoutDate = $fileInfo->getBasename('.' . $fileInfo->getExtension());
        }

        return new $className(
            $fileInfo,
            $fileInfo->getRelativePathname(),
            $fileInfo->getPathname(),
            $filenameWithoutDate,
            $dateTime
        );
    }
}
