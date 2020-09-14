<?php

declare(strict_types=1);

namespace Symplify\Autodiscovery;

use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

/**
 * @see \Symplify\Autodiscovery\Tests\NamespaceDetector\NamespaceDetectorTest
 */
final class NamespaceDetector
{
    /**
     * @var string
     */
    private const ENTITY_CLASS_NAME_PATTERN = '#entity\s+name="(?<className>.*?)"#';

    /**
     * @var string
     */
    private const NAMESPACE_NAME_PATTERN = '#namespace(\s+)(?<namespace>[\w\\\\]*?);#';

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(SmartFileSystem $smartFileSystem)
    {
        $this->smartFileSystem = $smartFileSystem;
    }

    public function detectFromDirectory(SmartFileInfo $directoryInfo): ?string
    {
        $filesInDirectory = (array) glob($directoryInfo->getRealPath() . '/*.php');
        if (count($filesInDirectory) === 0) {
            return null;
        }

        /** @var string $entityFilePath */
        $entityFilePath = array_pop($filesInDirectory);

        return $this->detectFromFile($entityFilePath);
    }

    public function detectFromXmlFileInfo(SmartFileInfo $entityXmlFileInfo): ?string
    {
        $contents = $entityXmlFileInfo->getContents();

        $match = Strings::match($contents, self::ENTITY_CLASS_NAME_PATTERN);
        if (! isset($match['className'])) {
            return null;
        }

        return Strings::before($match['className'], '\\', -1);
    }

    private function detectFromFile(string $filePath): ?string
    {
        $fileContent = $this->smartFileSystem->readFile($filePath);
        $match = Strings::match($fileContent, self::NAMESPACE_NAME_PATTERN);

        if (! isset($match['namespace'])) {
            return null;
        }

        return $match['namespace'];
    }
}
