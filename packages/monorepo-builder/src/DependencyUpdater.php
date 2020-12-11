<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder;

use Nette\Utils\Strings;
use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\SmartFileSystem\SmartFileInfo;

final class DependencyUpdater
{
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
     * @param string[] $packageNames
     */
    public function updateFileInfosWithPackagesAndVersion(
        array $smartFileInfos,
        array $packageNames,
        string $version
    ): void {
        foreach ($smartFileInfos as $packageComposerFileInfo) {
            $json = $this->jsonFileManager->loadFromFileInfo($packageComposerFileInfo);

            $json = $this->processSectionWithPackages($json, $packageNames, $version, ComposerJsonSection::REQUIRE);

            $json = $this->processSectionWithPackages($json, $packageNames, $version, ComposerJsonSection::REQUIRE_DEV);

            $this->jsonFileManager->printJsonToFileInfo($json, $packageComposerFileInfo);
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
        foreach ($smartFileInfos as $packageComposerFileInfo) {
            $json = $this->jsonFileManager->loadFromFileInfo($packageComposerFileInfo);

            $json = $this->processSection($json, $vendor, $version, ComposerJsonSection::REQUIRE);
            $json = $this->processSection($json, $vendor, $version, ComposerJsonSection::REQUIRE_DEV);

            $this->jsonFileManager->printJsonToFileInfo($json, $packageComposerFileInfo);
        }
    }

    /**
     * @param mixed[] $json
     * @param string[] $packageNames
     * @return mixed[]
     */
    private function processSectionWithPackages(
        array $json,
        array $packageNames,
        string $targetVersion,
        string $section
    ): array {
        if (! isset($json[$section])) {
            return $json;
        }

        $sectionKeys = array_keys($json[$section]);
        foreach ($sectionKeys as $packageName) {
            if (! in_array($packageName, $packageNames, true)) {
                continue;
            }

            $json[$section][$packageName] = $targetVersion;
        }

        return $json;
    }

    /**
     * @param mixed[] $json
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
        if (! Strings::startsWith($packageName, $vendor)) {
            return true;
        }

        return $packageVersion === $targetVersion;
    }
}
