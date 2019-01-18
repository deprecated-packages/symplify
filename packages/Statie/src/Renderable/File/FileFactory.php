<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\File;

use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\Utils\PathAnalyzer;

final class FileFactory
{
    /**
     * @var PathAnalyzer
     */
    private $pathAnalyzer;

    /**
     * @var StatieConfiguration
     */
    private $statieConfiguration;

    public function __construct(PathAnalyzer $pathAnalyzer, StatieConfiguration $statieConfiguration)
    {
        $this->pathAnalyzer = $pathAnalyzer;
        $this->statieConfiguration = $statieConfiguration;
    }

    /**
     * @param SmartFileInfo[] $fileInfos
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

    public function createFromFileInfo(SmartFileInfo $smartFileInfo): AbstractFile
    {
        return new File(
            $smartFileInfo,
            $smartFileInfo->getRelativeFilePathFromDirectory($this->statieConfiguration->getSourceDirectory()),
            $smartFileInfo->getRealPath(),
            $this->pathAnalyzer->detectFilenameWithoutDate($smartFileInfo),
            $this->pathAnalyzer->detectDate($smartFileInfo)
        );
    }
}
