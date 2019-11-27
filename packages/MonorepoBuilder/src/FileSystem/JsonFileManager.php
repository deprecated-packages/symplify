<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\FileSystem;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Symplify\PackageBuilder\Configuration\EolConfiguration;
use Symplify\SmartFileSystem\SmartFileInfo;

final class JsonFileManager
{
    /**
     * @var string[]
     */
    private $inlineSections = [];

    /**
     * @var SymfonyFilesystem
     */
    private $symfonyFilesystem;

    /**
     * @param string[] $inlineSections
     */
    public function __construct(SymfonyFilesystem $symfonyFilesystem, array $inlineSections)
    {
        $this->symfonyFilesystem = $symfonyFilesystem;
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
        return Json::decode(FileSystem::read($filePath), Json::FORCE_ARRAY);
    }

    /**
     * @param mixed[] $json
     */
    public function saveJsonWithFileInfo(array $json, SmartFileInfo $smartFileInfo): void
    {
        $jsonString = $this->encodeJsonToFileContent($json, $this->inlineSections);
        $this->symfonyFilesystem->dumpFile($smartFileInfo->getPathname(), $jsonString);
    }

    /**
     * @param mixed[] $json
     */
    public function saveJsonWithFilePath(array $json, string $filePath): void
    {
        $jsonString = $this->encodeJsonToFileContent($json, $this->inlineSections);
        $this->symfonyFilesystem->dumpFile($filePath, $jsonString);
    }

    /**
     * @param mixed[] $json
     * @param string[] $inlineSections
     */
    public function encodeJsonToFileContent(array $json, array $inlineSections = []): string
    {
        $jsonContent = Json::encode($json, Json::PRETTY) . EolConfiguration::getEolChar();

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
}
