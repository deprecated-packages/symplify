<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\FileSystem;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Symfony\Component\Finder\SplFileInfo;

final class JsonFileManager
{
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
        file_put_contents($fileInfo->getPathname(), $this->encodeJsonToFileContent($json));
    }

    /**
     * @param mixed[] $json
     */
    public function saveJsonWithFilePath(array $json, string $filePath): void
    {
        file_put_contents($filePath, $this->encodeJsonToFileContent($json));
    }

    /**
     * @param mixed[] $json
     */
    private function encodeJsonToFileContent(array $json): string
    {
        return Json::encode($json, Json::PRETTY) . PHP_EOL;
    }
}
