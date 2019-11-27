<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Package;

use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PackageNamesProvider
{
    /**
     * @var string[]
     */
    private $names = [];

    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    public function __construct(ComposerJsonProvider $composerJsonProvider, JsonFileManager $jsonFileManager)
    {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->jsonFileManager = $jsonFileManager;
    }

    /**
     * @return string[]
     */
    public function provide(): array
    {
        if ($this->names !== []) {
            return $this->names;
        }

        foreach ($this->composerJsonProvider->getPackagesFileInfos() as $splFileInfo) {
            $name = $this->extractNameFromFileInfo($splFileInfo);
            if ($name !== null) {
                $this->names[] = $name;
            }
        }

        return $this->names;
    }

    private function extractNameFromFileInfo(SmartFileInfo $smartFileInfo): ?string
    {
        $json = $this->jsonFileManager->loadFromFileInfo($smartFileInfo);

        return $json['name'] ?? null;
    }
}
