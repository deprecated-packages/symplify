<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder;

use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\Merge\Configuration\ModifyingComposerJsonProvider;
use Symplify\MonorepoBuilder\ValueObject\Section;
use Symplify\SmartFileSystem\SmartFileInfo;

final class VersionValidator
{
    /**
     * @var string[]
     */
    private const SECTIONS = [Section::REQUIRE, Section::REQUIRE_DEV];

    /**
     * @var string
     */
    private const MONOREPO_BUILDER_YAML = 'monorepo-builder.yaml';

    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    /**
     * @var ModifyingComposerJsonProvider
     */
    private $modifyingComposerJsonProvider;

    public function __construct(
        JsonFileManager $jsonFileManager,
        ModifyingComposerJsonProvider $modifyingComposerJsonProvider
    ) {
        $this->jsonFileManager = $jsonFileManager;
        $this->modifyingComposerJsonProvider = $modifyingComposerJsonProvider;
    }

    /**
     * @param SmartFileInfo[] $smartFileInfos
     * @return string[][]
     */
    public function findConflictingPackageVersionsInFileInfos(array $smartFileInfos): array
    {
        $packageVersionsPerFile = [];
        $packageVersionsPerFile = $this->appendAppendingComposerJson($packageVersionsPerFile);

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
    private function appendAppendingComposerJson(array $packageVersionsPerFile): array
    {
        $appendingComposerJson = $this->modifyingComposerJsonProvider->getAppendingComposerJson();
        if ($appendingComposerJson === null) {
            return $packageVersionsPerFile;
        }

        foreach ($appendingComposerJson->getRequire() as $packageName => $packageVersion) {
            $packageVersionsPerFile[$packageName][self::MONOREPO_BUILDER_YAML] = $packageVersion;
        }

        foreach ($appendingComposerJson->getRequireDev() as $packageName => $packageVersion) {
            $packageVersionsPerFile[$packageName][self::MONOREPO_BUILDER_YAML] = $packageVersion;
        }

        return $packageVersionsPerFile;
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
