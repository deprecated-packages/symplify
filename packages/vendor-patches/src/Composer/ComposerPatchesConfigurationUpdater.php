<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Composer;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Symplify\PackageBuilder\Yaml\ParametersMerger;

/**
 * @see \Symplify\VendorPatches\Tests\Composer\ComposerPatchesConfigurationUpdater\ComposerPatchesConfigurationUpdaterTest
 */
final class ComposerPatchesConfigurationUpdater
{
    public function __construct(
        private ParametersMerger $parametersMerger
    ) {
    }

    /**
     * @api
     * @param mixed[] $composerExtraPatches
     * @return mixed[]
     */
    public function updateComposerJson(string $composerJsonFilePath, array $composerExtraPatches): array
    {
        $addedComposerJson = [
            'extra' => [
                'patches' => $composerExtraPatches,
            ],
        ];

        $composerFileContents = FileSystem::read($composerJsonFilePath);
        $composerJson = Json::decode($composerFileContents, Json::FORCE_ARRAY);

        // merge "extra" section - deep merge is needed, so original patches are included
        return $this->parametersMerger->merge($composerJson, $addedComposerJson);
    }

    /**
     * @param mixed[] $composerExtraPatches
     */
    public function updateComposerJsonAndPrint(string $composerJsonFilePath, array $composerExtraPatches): void
    {
        $composerJson = $this->updateComposerJson($composerJsonFilePath, $composerExtraPatches);

        // print composer.json
        $composerJsonFileContents = Json::encode($composerJson, Json::PRETTY);
        FileSystem::write($composerJsonFilePath, $composerJsonFileContents);
    }
}
