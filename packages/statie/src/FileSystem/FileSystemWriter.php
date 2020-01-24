<?php

declare(strict_types=1);

namespace Symplify\Statie\FileSystem;

use Nette\Utils\FileSystem;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\Contract\File\RenderableFileInterface;

final class FileSystemWriter
{
    /**
     * @var StatieConfiguration
     */
    private $statieConfiguration;

    public function __construct(StatieConfiguration $statieConfiguration)
    {
        $this->statieConfiguration = $statieConfiguration;
    }

    /**
     * @param SmartFileInfo[] $files
     */
    public function copyStaticFiles(array $files): void
    {
        foreach ($files as $file) {
            $relativePathToSource = $file->getRelativeFilePathFromDirectory(
                $this->statieConfiguration->getSourceDirectory()
            );
            $absoluteDestination = $this->statieConfiguration->getOutputDirectory() . DIRECTORY_SEPARATOR . $relativePathToSource;

            FileSystem::copy($file->getRelativeFilePath(), $absoluteDestination, true);
        }
    }

    /**
     * @param RenderableFileInterface[] $files
     */
    public function renderFiles(array $files): void
    {
        foreach ($files as $file) {
            $absoluteDestination = $this->statieConfiguration->getOutputDirectory()
                . DIRECTORY_SEPARATOR
                . $file->getOutputPath();

            FileSystem::write($absoluteDestination, $file->getContent());
        }
    }
}
