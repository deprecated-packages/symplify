<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder;

use Nette\Utils\Strings;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\Composer\Section;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;

final class InterdependencyUpdater
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
     * @param SplFileInfo[] $fileInfos
     * @param string[] $packageNames
     */
    public function updateFileInfosWithPackagesAndVersion(array $fileInfos, array $packageNames, string $version): void
    {
        foreach ($fileInfos as $packageComposerFileInfo) {
            $json = $this->jsonFileManager->loadFromFileInfo($packageComposerFileInfo);

            $json = $this->processSectionWithPackages($json, $packageNames, $version, Section::REQUIRE);
            $json = $this->processSectionWithPackages($json, $packageNames, $version, Section::REQUIRE_DEV);

            $this->jsonFileManager->saveJsonWithFileInfo($json, $packageComposerFileInfo);
        }
    }

    /**
     * @param SplFileInfo[] $fileInfos
     */
    public function updateFileInfosWithVendorAndVersion(array $fileInfos, string $vendor, string $version): void
    {
        foreach ($fileInfos as $packageComposerFileInfo) {
            $json = $this->jsonFileManager->loadFromFileInfo($packageComposerFileInfo);

            $json = $this->processSection($json, $vendor, $version, Section::REQUIRE);
            $json = $this->processSection($json, $vendor, $version, Section::REQUIRE_DEV);

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
        string $section
    ): array {
        if (! isset($json[$section])) {
            return $json;
        }

        foreach (array_keys($json[$section]) as $packageName) {
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
