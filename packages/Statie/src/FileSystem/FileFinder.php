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
    public function findLayoutsAndSnippets(string $directory): array
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
        $path = $this->normalizePath($directoryInfo->getFilename() . DIRECTORY_SEPARATOR);
        $pathPattern = '#' . preg_quote($path, '#') . '#';

        $finder = Finder::create()->files()
            ->in($directoryInfo->getPath())
            ->path($pathPattern);

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
            ->name('*.twig')
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

    private function normalizePath(string $path): string
    {
        return strtr($path, DIRECTORY_SEPARATOR, '/');
    }
}
