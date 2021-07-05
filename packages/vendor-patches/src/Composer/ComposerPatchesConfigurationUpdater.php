<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Composer;

use Symplify\Astral\Exception\ShouldNotHappenException;
use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\PackageBuilder\Yaml\ParametersMerger;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\VendorPatches\Tests\Composer\ComposerPatchesConfigurationUpdater\ComposerPatchesConfigurationUpdaterTest
 */
final class ComposerPatchesConfigurationUpdater
{
    public function __construct(
        private ComposerJsonFactory $composerJsonFactory,
        private JsonFileManager $jsonFileManager,
        private ParametersMerger $parametersMerger
    ) {
    }

    /**
     * @param mixed[] $composerExtraPatches
     */
    public function updateComposerJson(string $composerJsonFilePath, array $composerExtraPatches): ComposerJson
    {
        $extra = [
            'patches' => $composerExtraPatches,
        ];

        $composerJson = $this->composerJsonFactory->createFromFilePath($composerJsonFilePath);

        // merge "extra" section - deep merge is needed, so original patches are included

        $newExtra = $this->parametersMerger->merge($composerJson->getExtra(), $extra);
        $composerJson->setExtra($newExtra);

        return $composerJson;
    }

    /**
     * @param mixed[] $composerExtraPatches
     */
    public function updateComposerJsonAndPrint(string $composerJsonFilePath, array $composerExtraPatches): void
    {
        $composerJson = $this->updateComposerJson($composerJsonFilePath, $composerExtraPatches);

        $fileInfo = $composerJson->getFileInfo();
        if (! $fileInfo instanceof SmartFileInfo) {
            throw new ShouldNotHappenException();
        }

        $this->jsonFileManager->printComposerJsonToFilePath($composerJson, $fileInfo->getRealPath());
    }
}
