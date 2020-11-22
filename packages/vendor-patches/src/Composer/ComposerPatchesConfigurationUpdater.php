<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Composer;

use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;

final class ComposerPatchesConfigurationUpdater
{
    /**
     * @var ComposerJsonFactory
     */
    private $composerJsonFactory;

    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    public function __construct(ComposerJsonFactory $composerJsonFactory, JsonFileManager $jsonFileManager)
    {
        $this->composerJsonFactory = $composerJsonFactory;
        $this->jsonFileManager = $jsonFileManager;
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
