<?php

declare(strict_types=1);

namespace Symplify\ComposerJsonManipulator;

use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\ComposerJsonManipulator\Tests\ComposerJsonFactory\ComposerJsonFactoryTest
 */
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

        $composerJson = $this->createFromArray($jsonArray);
        $composerJson->setOriginalFileInfo($smartFileInfo);

        return $composerJson;
    }

    public function createFromFilePath(string $filePath): ComposerJson
    {
        $jsonArray = $this->jsonFileManager->loadFromFilePath($filePath);

        $composerJson = $this->createFromArray($jsonArray);
        $fileInfo = new SmartFileInfo($filePath);
        $composerJson->setOriginalFileInfo($fileInfo);

        return $composerJson;
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

        if (isset($jsonArray['repositories'])) {
            $composerJson->setRepositories($jsonArray['repositories']);
        }

        $orderedKeys = array_keys($jsonArray);
        $composerJson->setOrderedKeys($orderedKeys);

        return $composerJson;
    }
}
