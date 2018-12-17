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
