<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\File;

use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;

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

    public function createFromFilePath(string $filePath): AbstractFile
    {
        return $this->createFromFileInfo(new SplFileInfo($filePath));
    }

    /**
     * @return File|PostFile
     */
    public function createFromFileInfo(SplFileInfo $file): AbstractFile
    {
        $relativeSource = substr($file->getPathname(), strlen($this->configuration->getSourceDirectory()));
        $relativeSource = ltrim($relativeSource, DIRECTORY_SEPARATOR);

        return new File($file, $relativeSource, $file->getPathname());
    }
}
