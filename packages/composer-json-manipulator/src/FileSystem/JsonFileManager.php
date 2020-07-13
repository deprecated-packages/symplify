<?php

declare(strict_types=1);

namespace Symplify\ComposerJsonManipulator\FileSystem;

use Nette\Utils\Json;
use Nette\Utils\Strings;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\PackageBuilder\Configuration\StaticEolConfiguration;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class JsonFileManager
{
    /**
     * @var string[]
     */
    private $inlineSections = [];

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @param string[] $inlineSections
     */
    public function __construct(SmartFileSystem $smartFileSystem, array $inlineSections = [])
    {
        $this->smartFileSystem = $smartFileSystem;
        $this->inlineSections = $inlineSections;
    }

    /**
     * @return mixed[]
     */
    public function loadFromFileInfo(SmartFileInfo $smartFileInfo): array
    {
        return Json::decode($smartFileInfo->getContents(), Json::FORCE_ARRAY);
    }

    /**
     * @return mixed[]
     */
    public function loadFromFilePath(string $filePath): array
    {
        $fileContent = $this->smartFileSystem->readFile($filePath);

        return Json::decode($fileContent, Json::FORCE_ARRAY);
    }

    /**
     * @param mixed[] $json
     */
    public function saveJsonWithFileInfo(array $json, SmartFileInfo $smartFileInfo): void
    {
        $jsonString = $this->encodeJsonToFileContent($json, $this->inlineSections);
        $this->smartFileSystem->dumpFile($smartFileInfo->getPathname(), $jsonString);
    }

    public function saveComposerJsonToFilePath(ComposerJson $composerJson, string $filePath): void
    {
        $jsonString = $this->encodeJsonToFileContent($composerJson->getJsonArray(), $this->inlineSections);
        $this->smartFileSystem->dumpFile($filePath, $jsonString);
    }

    public function saveComposerJsonWithFileInfo(ComposerJson $composerJson, SmartFileInfo $smartFileInfo): void
    {
        $this->saveJsonWithFileInfo($composerJson->getJsonArray(), $smartFileInfo);
    }

    /**
     * @param mixed[] $json
     * @param string[] $inlineSections
     */
    public function encodeJsonToFileContent(array $json, array $inlineSections = []): string
    {
        // Empty arrays may lead to bad encoding since we can't be sure whether they need to be arrays or objects.
        $json = $this->removeEmptyKeysFromJsonArray($json);
        $jsonContent = Json::encode($json, Json::PRETTY) . StaticEolConfiguration::getEolChar();

        foreach ($inlineSections as $inlineSection) {
            $pattern = '#("' . preg_quote($inlineSection, '#') . '": )\[(.*?)\](,)#ms';

            $jsonContent = Strings::replace($jsonContent, $pattern, function (array $match): string {
                $inlined = Strings::replace($match[2], '#\s+#', ' ');
                $inlined = trim($inlined);
                $inlined = '[' . $inlined . ']';

                return $match[1] . $inlined . $match[3];
            });
        }

        return $jsonContent;
    }

    private function removeEmptyKeysFromJsonArray(array $json): array
    {
        foreach ($json as $key => $value) {
            if (! is_array($value)) {
                continue;
            }

            if (count($value) === 0) {
                unset($json[$key]);
            } else {
                $json[$key] = $this->removeEmptyKeysFromJsonArray($value);
            }
        }

        return $json;
    }
}
