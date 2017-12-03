<?php declare(strict_types=1);

namespace Symplify\Statie\FileSystem;

use Nette\Utils\FileSystem;
use SplFileInfo;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Renderable\File\AbstractFile;

final class FileSystemWriter
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param SplFileInfo[] $files
     */
    public function copyStaticFiles(array $files): void
    {
        foreach ($files as $file) {
            $relativeDestination = substr($file->getPathname(), strlen($this->configuration->getSourceDirectory()));
            $absoluteDestination = $this->configuration->getOutputDirectory() . $relativeDestination;

            FileSystem::copy($file->getRealPath(), $absoluteDestination, true);
        }
    }

    /**
     * @param AbstractFile[] $files
     */
    public function copyRenderableFiles(array $files): void
    {
        foreach ($files as $file) {
            $absoluteDestination = $this->configuration->getOutputDirectory()
                . DIRECTORY_SEPARATOR
                . $file->getOutputPath();

            FileSystem::createDir(dirname($absoluteDestination));
            file_put_contents($absoluteDestination, $file->getContent());
        }
    }
}
