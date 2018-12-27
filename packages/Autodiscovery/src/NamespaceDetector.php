<?php declare(strict_types=1);

namespace Symplify\Autodiscovery;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use function Safe\glob;

final class NamespaceDetector
{
    public function detectFromDirectory(SmartFileInfo $directoryInfo): ?string
    {
        $filesInDirectory = glob($directoryInfo->getRealPath() . '/*.php');
        if (! count($filesInDirectory)) {
            return null;
        }

        $entityFilePath = array_pop($filesInDirectory);

        return $this->detectFromFile($entityFilePath);
    }

    public function detectFromXmlFileInfo(SmartFileInfo $entityXmlFileInfo): ?string
    {
        $fileContent = $entityXmlFileInfo->getContents();

        $match = Strings::match($fileContent, '#entity name="(?<className>.*?)"#');
        if (! isset($match['className'])) {
            return null;
        }

        $result = Strings::before($match['className'], '\\', -1);
        if ($result === false) {
            return null;
        }

        return $result;
    }

    private function detectFromFile(string $filePath): ?string
    {
        $fileContent = FileSystem::read($filePath);
        $match = Strings::match($fileContent, '#namespace(\s+)(?<namespace>[\w\\\\]*?);#');

        if (! isset($match['namespace'])) {
            return null;
        }

        return $match['namespace'];
    }
}
