<?php declare(strict_types=1);

namespace Symplify\NeonToYamlConverter\Finder;

use Symfony\Component\Finder\Finder;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class NeonAndYamlFinder
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
    public function findYamlFilesInfSource(string $source): array
    {
        if (is_file($source) && file_exists($source)) {
            return [new SmartFileInfo($source)];
        }

        return $this->findFilesInDirectoryBySuffix($source, '(yml|yaml)');
    }

    /**
     * @return SmartFileInfo[]
     */
    public function findNeonFilesInSource(string $source): array
    {
        if (is_file($source) && file_exists($source)) {
            return [new SmartFileInfo($source)];
        }

        return $this->findFilesInDirectoryBySuffix($source, 'neon');
    }

    /**
     * @return SmartFileInfo[]
     */
    private function findFilesInDirectoryBySuffix(string $sourceDirectory, string $suffix): array
    {
        $twigFileFinder = Finder::create()
            ->files()
            ->in($sourceDirectory)
            ->name('#\.' . $suffix . '$#')
            ->sortByName();

        return $this->finderSanitizer->sanitize($twigFileFinder);
    }
}
