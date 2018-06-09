<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\FileSystem;

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
        return Json::decode(file_get_contents($filePath), Json::FORCE_ARRAY);
    }

    /**
     * @param mixed[] $json
     */
    public function saveJsonWithFileInfo(array $json, SplFileInfo $fileInfo): void
    {
        $fileContent = Json::encode($json, Json::PRETTY) . PHP_EOL;

        file_put_contents($fileInfo->getPath(), $fileContent);
    }

    /**
     * @param mixed[] $json
     */
    public function saveJsonWithFilePath(array $json, string $filePath): void
    {
        $fileContent = Json::encode($json, Json::PRETTY) . PHP_EOL;

        file_put_contents($filePath, $fileContent);
    }
}
