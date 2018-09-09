<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\FileSystem;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Symfony\Component\Finder\SplFileInfo;

final class JsonFileManager
{
    /**
     * @var SymfonyFilesystem
     */
    private $symfonyFilesystem;

    public function __construct(SymfonyFilesystem $symfonyFilesystem)
    {
        $this->symfonyFilesystem = $symfonyFilesystem;
    }

    /**
     * @return mixed[]
     */
    public function loadFromFileInfo(SplFileInfo $fileInfo): array
    {
        return Json::decode($fileInfo->getContents(), Json::FORCE_ARRAY);
    }

    /**
     * @return mixed[]
     */
    public function loadFromFilePath(string $filePath): array
    {
        return Json::decode(FileSystem::read($filePath), Json::FORCE_ARRAY);
    }

    /**
     * @param mixed[] $json
     */
    public function saveJsonWithFileInfo(array $json, SplFileInfo $fileInfo): void
    {
        $this->symfonyFilesystem->dumpFile($fileInfo->getPathname(), $this->encodeJsonToFileContent($json));
    }

    /**
     * @param mixed[] $json
     */
    public function saveJsonWithFilePath(array $json, string $filePath): void
    {
        $this->symfonyFilesystem->dumpFile($filePath, $this->encodeJsonToFileContent($json));
    }

    /**
     * @param mixed[] $json
     */
    private function encodeJsonToFileContent(array $json): string
    {
        return Json::encode($json, Json::PRETTY) . PHP_EOL;
    }
}
