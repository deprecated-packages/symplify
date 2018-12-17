<?php declare(strict_types=1);

namespace Symplify\Autodiscovery;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class AutodiscovererCollector
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
        $this->projectDir = $containerBuilder->getParameter('kernel.project_dir');
        $this->finderSanitizer = new FinderSanitizer();
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

        return $this->finderSanitizer->sanitize($finder->getIterator());
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
