<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonObject;

use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ComposerJsonFactory
{
    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    public function __construct(JsonFileManager $jsonFileManager)
    {
        $this->jsonFileManager = $jsonFileManager;
    }

    public function createFromFileInfo(SmartFileInfo $smartFileInfo): ComposerJson
    {
        $jsonArray = $this->jsonFileManager->loadFromFilePath($smartFileInfo->getRealPath());

        return $this->createFromArray($jsonArray);
    }

    public function createFromFilePath(string $filePath): ComposerJson
    {
        $jsonArray = $this->jsonFileManager->loadFromFilePath($filePath);

        return $this->createFromArray($jsonArray);
    }

    public function createFromArray(array $jsonArray): ComposerJson
    {
        $composerJson = new ComposerJson();

        if (isset($jsonArray['name'])) {
            $composerJson->setName($jsonArray['name']);
        }

        if (isset($jsonArray['description'])) {
            $composerJson->setDescription($jsonArray['description']);
        }

        if (isset($jsonArray['license'])) {
            $composerJson->setLicense($jsonArray['license']);
        }

        if (isset($jsonArray['require'])) {
            $composerJson->setRequire($jsonArray['require']);
        }

        if (isset($jsonArray['require-dev'])) {
            $composerJson->setRequireDev($jsonArray['require-dev']);
        }

        if (isset($jsonArray['autoload'])) {
            $composerJson->setAutoload($jsonArray['autoload']);
        }

        if (isset($jsonArray['autoload-dev'])) {
            $composerJson->setAutoloadDev($jsonArray['autoload-dev']);
        }

        if (isset($jsonArray['replace'])) {
            $composerJson->setReplace($jsonArray['replace']);
        }

        if (isset($jsonArray['config'])) {
            $composerJson->setConfig($jsonArray['config']);
        }

        if (isset($jsonArray['extra'])) {
            $composerJson->setExtra($jsonArray['extra']);
        }

        if (isset($jsonArray['scripts'])) {
            $composerJson->setScripts($jsonArray['scripts']);
        }

        if (isset($jsonArray['minimum-stability'])) {
            $composerJson->setMinimumStability($jsonArray['minimum-stability']);
        }

        if (isset($jsonArray['prefer-stable'])) {
            $composerJson->setPreferStable($jsonArray['prefer-stable']);
        }

        $orderedKeys = array_keys($jsonArray);
        $composerJson->setOrderedKeys($orderedKeys);

        // @todo the rest

        return $composerJson;
    }
}
