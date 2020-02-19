<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder;

use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\ValueObject\Section;
use Symplify\SmartFileSystem\SmartFileInfo;

final class VersionValidator
{
    /**
     * @var string[]
     */
    private const SECTIONS = [Section::REQUIRE, Section::REQUIRE_DEV];

    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    public function __construct(JsonFileManager $jsonFileManager)
    {
        $this->jsonFileManager = $jsonFileManager;
    }

    /**
     * @param SmartFileInfo[] $smartFileInfos
     * @return string[][]
     */
    public function findConflictingPackageVersionsInFileInfos(array $smartFileInfos): array
    {
        $packageVersionsPerFile = [];

        foreach ($smartFileInfos as $smartFileInfo) {
            $json = $this->jsonFileManager->loadFromFileInfo($smartFileInfo);

            foreach (self::SECTIONS as $section) {
                if (! isset($json[$section])) {
                    continue;
                }

                foreach ($json[$section] as $packageName => $packageVersion) {
                    $packageVersionsPerFile[$packageName][$smartFileInfo->getPathname()] = $packageVersion;
                }
            }
        }

        return $this->filterConflictingPackageVersionsPerFile($packageVersionsPerFile);
    }

    /**
     * @param mixed[] $packageVersionsPerFile
     * @return mixed[]
     */
    private function filterConflictingPackageVersionsPerFile(array $packageVersionsPerFile): array
    {
        $conflictingPackageVersionsPerFile = [];
        foreach ($packageVersionsPerFile as $packageName => $filesToVersions) {
            $uniqueVersions = array_unique($filesToVersions);
            if (count($uniqueVersions) <= 1) {
                continue;
            }

            // sort by versions to make more readable
            asort($filesToVersions);

            $conflictingPackageVersionsPerFile[$packageName] = $filesToVersions;
        }

        return $conflictingPackageVersionsPerFile;
    }
}
