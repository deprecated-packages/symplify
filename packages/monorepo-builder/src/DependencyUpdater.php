<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder;

use Nette\Utils\Strings;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\ValueObject\Section;
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
        string $version,
        ?callable $shouldSkipCallable = null
    ): void {
        foreach ($smartFileInfos as $packageComposerFileInfo) {
            $json = $this->jsonFileManager->loadFromFileInfo($packageComposerFileInfo);

            $json = $this->processSectionWithPackages(
                $json,
                $packageNames,
                $version,
                Section::REQUIRE,
                $packageComposerFileInfo,
                $shouldSkipCallable
            );
            $json = $this->processSectionWithPackages(
                $json,
                $packageNames,
                $version,
                Section::REQUIRE_DEV,
                $packageComposerFileInfo,
                $shouldSkipCallable
            );

            $this->jsonFileManager->saveJsonWithFileInfo($json, $packageComposerFileInfo);
        }
    }

    /**
     * @param SmartFileInfo[] $smartFileInfos
     */
    public function updateFileInfosWithVendorAndVersion(
        array $smartFileInfos,
        string $vendor,
        string $version,
        ?callable $shouldSkipCallable = null
    ): void {
        foreach ($smartFileInfos as $packageComposerFileInfo) {
            $json = $this->jsonFileManager->loadFromFileInfo($packageComposerFileInfo);

            $json = $this->processSection(
                $json,
                $vendor,
                $version,
                Section::REQUIRE,
                $packageComposerFileInfo,
                $shouldSkipCallable
            );
            $json = $this->processSection(
                $json,
                $vendor,
                $version,
                Section::REQUIRE_DEV,
                $packageComposerFileInfo,
                $shouldSkipCallable
            );
            $this->jsonFileManager->saveJsonWithFileInfo($json, $packageComposerFileInfo);
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
        string $section,
        SmartFileInfo $smartFileInfo,
        ?callable $shouldSkipCallback = null
    ): array {
        if (! isset($json[$section])) {
            return $json;
        }

        foreach (array_keys($json[$section]) as $packageName) {
            if (! in_array($packageName, $packageNames, true)) {
                continue;
            }

            if ($shouldSkipCallback !== null && $shouldSkipCallback($smartFileInfo, $packageName, $section)) {
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
    private function processSection(
        array $json,
        string $vendor,
        string $targetVersion,
        string $section,
        SmartFileInfo $smartFileInfo,
        ?callable $shouldSkipCallback = null
    ): array {
        if (! isset($json[$section])) {
            return $json;
        }

        foreach ($json[$section] as $packageName => $packageVersion) {
            if ($this->shouldSkip($vendor, $targetVersion, $packageName, $packageVersion)) {
                continue;
            }

            if ($shouldSkipCallback !== null && $shouldSkipCallback($smartFileInfo, $packageName, $section)) {
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
