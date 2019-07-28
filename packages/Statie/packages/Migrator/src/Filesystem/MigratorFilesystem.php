<?php declare(strict_types=1);

namespace Symplify\Statie\Migrator\Filesystem;

use Nette\Utils\FileSystem;
use Symfony\Component\Finder\Finder;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

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
        if (! file_exists($directory)) {
            return [];
        }

        $finder = $this->createBasicFinder()
            ->in($directory)
            ->name('#\.(yml|yaml)$#');

        return $this->finderSanitizer->sanitize($finder);
    }

    /**
     * @return SmartFileInfo[]
     */
    public function findPostFiles(string $directory): array
    {
        if (! file_exists($directory)) {
            return [];
        }

        $finder = $this->createBasicFinder()
            ->name('#\.(html|md)$#')
            ->in($directory);

        return $this->finderSanitizer->sanitize($finder);
    }

    /**
     * @return SmartFileInfo[]
     */
    public function getIncorrectlyNamedMarkdownFiles(string $directory): array
    {
        if (! file_exists($directory)) {
            return [];
        }

        $finder = $this->createBasicFinder()
            ->name('#\.(markdown|mkdown|mkdn|mkd)$#')
            ->in($directory);

        return $this->finderSanitizer->sanitize($finder);
    }

    public function absolutizePath(string $path, string $workingDirectory): string
    {
        if (FileSystem::isAbsolute($path)) {
            return $path;
        }

        return $workingDirectory . '/' . ltrim($path, '/');
    }

    /**
     * @return SmartFileInfo[]
     */
    public function findFiles(string $path, string $workingDirectory): array
    {
        $path = $this->absolutizePath($path, $workingDirectory);
        if (! file_exists($path)) {
            return [];
        }

        $finder = $this->createBasicFinder()
            ->in($path);

        return $this->finderSanitizer->sanitize($finder);
    }

    /**
     * @return SmartFileInfo[]
     */
    public function findFilesWithGlob(string $globPattern, string $workingDirectory): array
    {
        $globPath = $workingDirectory . '/' . $globPattern;

        $foundFiles = glob($globPath);
        if ($foundFiles === false) {
            return [];
        }

        foreach ($foundFiles as $key => $foundFile) {
            // skip README.md
            if ($foundFile === $workingDirectory . '/README.md') {
                unset($foundFiles[$key]);
            }
        }

        return $this->finderSanitizer->sanitize($foundFiles);
    }

    /**
     * @return SmartFileInfo[]
     */
    public function findIncludeableFiles(string $path, string $workingDirectory): array
    {
        $path = $this->absolutizePath($path, $workingDirectory);
        if (! file_exists($path)) {
            return [];
        }

        $finder = $this->createBasicFinder()
            ->in($path)
            ->path('_layouts')
            ->path('_snippets');

        return $this->finderSanitizer->sanitize($finder);
    }

    /**
     * @return SmartFileInfo[]
     */
    public function getPossibleTwigFiles(string $path, string $workingDirectory): array
    {
        $path = $this->absolutizePath($path, $workingDirectory);
        if (! file_exists($path)) {
            return [];
        }

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
