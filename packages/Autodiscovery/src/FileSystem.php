<?php declare(strict_types=1);

namespace Symplify\Autodiscovery;

use Symfony\Component\Finder\Finder;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class FileSystem
{
    /**
     * @var string
     */
    private $projectDiretory;

    /**
     * @var string[]
     */
    private $packageDirectories = [];

    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    /**
     * @param string[] $packageDirectories
     */
    public function __construct(string $projectDirectory, array $packageDirectories = [])
    {
        $this->finderSanitizer = new FinderSanitizer();
        $this->projectDiretory = $projectDirectory;
        $this->packageDirectories = $packageDirectories;
    }

    /**
     * @return SmartFileInfo[]
     */
    public function getTemplatesDirectories(): array
    {
        return $this->getDirectoriesInSourceByName('templates');
    }

    /**
     * @return SmartFileInfo[]
     */
    public function getEntityDirectories(): array
    {
        return $this->getDirectoriesInSourceByName('Entity');
    }

    /**
     * @return SmartFileInfo[]
     */
    public function getControllerDirectories(): array
    {
        return $this->getDirectoriesInSourceByName('Controller');
    }

    /**
     * @return SmartFileInfo[]
     */
    public function getTranslationDirectories(): array
    {
        return $this->getDirectoriesInSourceByName('translations');
    }

    /**
     * @return SmartFileInfo[]
     */
    private function getDirectoriesInSourceByName(string $name): array
    {
        if (! $this->getDirectories()) {
            return [];
        }

        $finder = Finder::create()
            ->directories()
            ->name($name)
            ->in($this->getDirectories())
            ->notPath('#tests#');

        return $this->finderSanitizer->sanitize($finder);
    }

    /**
     * @return string[]
     */
    private function getDirectories(): array
    {
        $possibleDirectories = [
            $this->projectDiretory . '/src',
            $this->projectDiretory . '/templates',
            $this->projectDiretory . '/translations',
            $this->projectDiretory . '/packages',
            $this->projectDiretory . '/projects',
        ];

        $possibleDirectories = array_merge($possibleDirectories, $this->packageDirectories);

        $directories = [];
        foreach ($possibleDirectories as $possibleDirectory) {
            if (file_exists($possibleDirectory)) {
                $directories[] = $possibleDirectory;
            }
        }

        return $directories;
    }
}
