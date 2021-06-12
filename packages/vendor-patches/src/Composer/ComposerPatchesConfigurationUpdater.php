<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Composer;

use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;

final class ComposerPatchesConfigurationUpdater
{
    public function __construct(
        private ComposerJsonFactory $composerJsonFactory,
        private JsonFileManager $jsonFileManager
    ) {
    }

    /**
     * @param mixed[] $composerExtraPatches
     */
    public function updateComposerJson(array $composerExtraPatches): void
    {
        $extra = [
            'patches' => $composerExtraPatches,
        ];

        $composerJsonFilePath = getcwd() . '/composer.json';
        $composerJson = $this->composerJsonFactory->createFromFilePath($composerJsonFilePath);

        // merge "extra" section
        $newExtra = array_merge($composerJson->getExtra(), $extra);
        $composerJson->setExtra($newExtra);

        $this->jsonFileManager->printComposerJsonToFilePath($composerJson, $composerJsonFilePath);
    }
}
