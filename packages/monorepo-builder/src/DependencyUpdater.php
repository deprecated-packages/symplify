<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder;

use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\SmartFileSystem\SmartFileInfo;

final class DependencyUpdater
{
    public function __construct(
        private JsonFileManager $jsonFileManager
    ) {
    }

    /**
     * @param SmartFileInfo[] $smartFileInfos
     * @param string[] $packageNames
     */
    public function updateFileInfosWithPackagesAndVersion(
        array $smartFileInfos,
        array $packageNames,
        string $version
    ): void {
        foreach ($smartFileInfos as $smartFileInfo) {
            $json = $this->jsonFileManager->loadFromFileInfo($smartFileInfo);

            $json = $this->processSectionWithPackages($json, $packageNames, $version, ComposerJsonSection::REQUIRE);

            $json = $this->processSectionWithPackages($json, $packageNames, $version, ComposerJsonSection::REQUIRE_DEV);

            $this->jsonFileManager->printJsonToFileInfo($json, $smartFileInfo);
        }
    }

    /**
     * @param SmartFileInfo[] $smartFileInfos
     */
    public function updateFileInfosWithVendorAndVersion(
        array $smartFileInfos,
        string $vendor,
        string $version
    ): void {
        foreach ($smartFileInfos as $smartFileInfo) {
            $json = $this->jsonFileManager->loadFromFileInfo($smartFileInfo);

            $json = $this->processSection($json, $vendor, $version, ComposerJsonSection::REQUIRE);
            $json = $this->processSection($json, $vendor, $version, ComposerJsonSection::REQUIRE_DEV);

            $this->jsonFileManager->printJsonToFileInfo($json, $smartFileInfo);
        }
    }

    /**
     * @param mixed[] $json
     * @param string[] $parentPackageNames
     * @param ComposerJsonSection::* $section
     * @return mixed[]
     */
    private function processSectionWithPackages(
        array $json,
        array $parentPackageNames,
        string $targetVersion,
        string $section
    ): array {
        if (! isset($json[$section])) {
            return $json;
        }

        $packageNames = array_keys($json[$section]);
        foreach ($packageNames as $packageName) {
            if (! in_array($packageName, $parentPackageNames, true)) {
                continue;
            }

            $json[$section][$packageName] = $targetVersion;
        }

        return $json;
    }

    /**
     * @param mixed[] $json
     * @param ComposerJsonSection::* $section
     * @return mixed[]
     */
    private function processSection(array $json, string $vendor, string $targetVersion, string $section): array
    {
        if (! isset($json[$section])) {
            return $json;
        }

        foreach ($json[$section] as $packageName => $packageVersion) {
            if ($this->shouldSkip($vendor, $targetVersion, $packageName, $packageVersion)) {
                continue;
            }

            $json[$section][$packageName] = $targetVersion;
        }

        return $json;
    }

    private function shouldSkip(
        string $vendor,
        string $targetVersion,
        string $packageName,
        string $packageVersion
    ): bool {
        if (! \str_starts_with($packageName, $vendor)) {
            return true;
        }

        return $packageVersion === $targetVersion;
    }
}
