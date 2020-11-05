<?php

declare(strict_types=1);

namespace Symplify\ComposerJsonManipulator\Printer;

use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class ComposerJsonPrinter
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
     * @param string|SmartFileInfo $targetFile
     */
    public function print(ComposerJson $composerJson, $targetFile): string
    {
        if (is_string($targetFile)) {
            return $this->jsonFileManager->printComposerJsonToFilePath($composerJson, $targetFile);
        }

        if (! $targetFile instanceof SmartFileInfo) {
            throw new ShouldNotHappenException();
        }

        return $this->jsonFileManager->printJsonToFileInfo($composerJson->getJsonArray(), $targetFile);
    }
}
