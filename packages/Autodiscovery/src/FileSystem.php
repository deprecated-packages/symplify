<?php declare(strict_types=1);

namespace Symplify\Autodiscovery;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class FileSystem
{
    /**
     * @var string
     */
    private $projectDir;

    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    public function __construct(ContainerBuilder $containerBuilder)
    {
        $this->finderSanitizer = new FinderSanitizer();
        $this->projectDir = $containerBuilder->getParameter('kernel.project_dir');
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
        $possibleDirs = [
            $this->projectDir . '/src',
            $this->projectDir . '/templates',
            $this->projectDir . '/packages',
            $this->projectDir . '/projects',
            // WTF? this must be configurable
            __DIR__ . '/../../../../packages',
        ];

        $dirs = [];
        foreach ($possibleDirs as $possibleDir) {
            if (file_exists($possibleDir)) {
                $dirs[] = $possibleDir;
            }
        }

        return $dirs;
    }
}
