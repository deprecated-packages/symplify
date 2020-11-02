<?php
declare(strict_types=1);

namespace Symplify\ComposerJsonManipulator\Printer;

use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\SmartFileSystem\SmartFileInfo;

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

    public function print(ComposerJson $composerJson, SmartFileInfo $targetFileInfo): string
    {
        return $this->jsonFileManager->printJsonToFileInfo($composerJson->getJsonArray(), $targetFileInfo);
    }
}
