<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonManipulator\Printer;

use Symplify\MonorepoBuilder\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\ComposerJsonManipulator\ValueObject\ComposerJson;
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

    public function printToString(ComposerJson $composerJson): string
    {
        return $this->jsonFileManager->encodeJsonToFileContent($composerJson->getJsonArray());
    }

    public function print(ComposerJson $composerJson, string | SmartFileInfo $targetFile): void
    {
        if (is_string($targetFile)) {
            $this->jsonFileManager->printComposerJsonToFilePath($composerJson, $targetFile);
            return;
        }

        $this->jsonFileManager->printJsonToFileInfo($composerJson->getJsonArray(), $targetFile);
    }
}
