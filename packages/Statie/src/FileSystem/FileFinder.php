<?php declare(strict_types=1);

namespace Symplify\Statie\FileSystem;

use SplFileInfo as NativeSplFileInfo;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class FileFinder
{
    /**
     * @var string[]
     */
    private $staticFileExtensions = [
        'CNAME', '*.png', '*.jpg', '*.svg', '*.css', '*.ico', '*.js', '*.', '*.jpeg', '*.gif', '*.zip', '*.tgz', '*.gz',
        '*.rar', '*.bz2', '*.pdf', '*.txt', '*.tar', '*.mp3', '*.doc', '*.xls', '*.pdf', '*.ppt', '*.txt', '*.tar',
        '*.bmp', '*.rtf', '*.woff2', '*.woff', '*.otf', '*.ttf', '*.eot',
    ];

    /**
     * @return SplFileInfo[]
     */
    public function findLatteLayoutsAndSnippets(string $directory): array
    {
        $finder = Finder::create()->files()
            ->in($directory)
            ->path('#(_layouts|_snippets)#');

        return $this->getFilesFromFinder($finder);
    }

    /**
     * @return SplFileInfo[]
     */
    public function findInDirectoryForGenerator(string $directoryPath): array
    {
        $directoryInfo = new NativeSplFileInfo($directoryPath);
        $pathPattern = '#' . preg_quote($directoryInfo->getFilename() . DIRECTORY_SEPARATOR, '#') . '#';

        $finder = Finder::create()->files()
            ->in($directoryInfo->getPath())
            ->path($pathPattern)
            // sort by name descending
            ->sort(function (SplFileInfo $firstFileInfo, SplFileInfo $secondFileInfo): int {
                return strcmp($secondFileInfo->getRealPath(), $firstFileInfo->getRealPath());
            });

        return $this->getFilesFromFinder($finder);
    }

    /**
     * @return SplFileInfo[]
     */
    public function findStaticFiles(string $directory): array
    {
        $finder = Finder::create()->files()
            ->in($directory);

        foreach ($this->staticFileExtensions as $name) {
            $finder->name($name);
        }

        return $this->getFilesFromFinder($finder);
    }

    /**
     * @return SplFileInfo[]
     */
    public function findRestOfRenderableFiles(string $directory): array
    {
        $finder = Finder::create()->files()
            ->name('*.html')
            ->name('*.latte')
            ->name('*.rss')
            ->name('*.xml')
            ->notPath('#(_layouts|_snippets)#')
            ->in($directory);

        return $this->getFilesFromFinder($finder);
    }

    /**
     * @return SplFileInfo[]
     */
    private function getFilesFromFinder(Finder $finder): array
    {
        $files = [];
        foreach ($finder->getIterator() as $key => $file) {
            $files[$key] = $file;
        }

        return $files;
    }
}
