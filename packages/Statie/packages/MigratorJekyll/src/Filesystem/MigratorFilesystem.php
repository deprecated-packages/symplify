<?php declare(strict_types=1);

namespace Symplify\Statie\MigratorJekyll\Filesystem;

use Nette\Utils\FileSystem;
use Symfony\Component\Finder\Finder;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use function Safe\getcwd;
use function Safe\glob;

final class MigratorFilesystem
{
    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    public function __construct(FinderSanitizer $finderSanitizer)
    {
        $this->finderSanitizer = $finderSanitizer;
    }

    /**
     * @return SmartFileInfo[]
     */
    public function findYamlFiles(string $directory): array
    {
        $finder = $this->createBasicFinder()
            ->in($directory)
            ->name('#\.y(a)?ml$#');

        return $this->finderSanitizer->sanitize($finder);
    }

    /**
     * @return SmartFileInfo[]
     */
    public function findPostFiles(string $directory): array
    {
        $finder = $this->createBasicFinder()
            ->name('#\.(html|md)$#')
            ->in($directory);

        return $this->finderSanitizer->sanitize($finder);
    }

    public function absolutizePath(string $path): string
    {
        if (FileSystem::isAbsolute($path)) {
            return $path;
        }

        return getcwd() . '/' . ltrim($path, '/');
    }

    /**
     * @return SmartFileInfo[]
     */
    public function findFiles(string $path): array
    {
        $finder = $this->createBasicFinder()
            ->in($path);

        return $this->finderSanitizer->sanitize($finder);
    }

    /**
     * @return SmartFileInfo[]
     */
    public function findFilesWithGlob(string $globPattern): array
    {
        $globPath = getcwd() . '/' . $globPattern;

        $foundFiles = glob($globPath);
        foreach ($foundFiles as $key => $foundFile) {
            // skip README.md
            if ($foundFile === getcwd() . '/README.md') {
                unset($foundFiles[$key]);
            }
        }

        return $this->finderSanitizer->sanitize($foundFiles);
    }

    /**
     * @return SmartFileInfo[]
     */
    public function findIncludeableFiles(string $path): array
    {
        $finder = $this->createBasicFinder()
            ->in($path)
            ->path('_snippets');

        return $this->finderSanitizer->sanitize($finder);
    }

    /**
     * @return SmartFileInfo[]
     */
    public function getPossibleTwigFiles(string $path): array
    {
        $finder = $this->createBasicFinder()
            ->in($path)
            ->name('#\.(html|twig|xml|md)$#')
            ->notPath('_posts')
            ->notPath('_drafts');

        return $this->finderSanitizer->sanitize($finder);
    }

    private function createBasicFinder(): Finder
    {
        return Finder::create()
            ->files()
            ->exclude('vendor')
            ->exclude('src')
            ->sortByName();
    }
}
