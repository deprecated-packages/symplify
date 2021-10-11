<?php

declare(strict_types=1);

namespace Symplify\ComposerJsonManipulator\Printer;

use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @api
 */
final class ComposerJsonPrinter
{
    public function __construct(
        private JsonFileManager $jsonFileManager
    ) {
    }

    public function print(ComposerJson $composerJson, string | SmartFileInfo $targetFile): string
    {
        if (is_string($targetFile)) {
            return $this->jsonFileManager->printComposerJsonToFilePath($composerJson, $targetFile);
        }

        return $this->jsonFileManager->printJsonToFileInfo($composerJson->getJsonArray(), $targetFile);
    }
}
