<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Finder;

use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\FileSystemFilter;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

final class GenericFilesFinder
{
    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    /**
     * @var FileSystemFilter
     */
    private $fileSystemFilter;

    public function __construct(FinderSanitizer $finderSanitizer, FileSystemFilter $fileSystemFilter)
    {
        $this->finderSanitizer = $finderSanitizer;
        $this->fileSystemFilter = $fileSystemFilter;
    }

    /**
     * @return SmartFileInfo[]
     */
    public function find(array $directoriesOrFiles, string $name): array
    {
        $directories = $this->fileSystemFilter->filterDirectories($directoriesOrFiles);

        $finder = new Finder();
        $finder->name($name)
            ->in($directories)
            ->files();

        $fileInfos = $this->finderSanitizer->sanitize($finder);

        $files = $this->fileSystemFilter->filterFiles($directoriesOrFiles);
        foreach ($files as $file) {
            $fileInfos[] = new SmartFileInfo($file);
        }

        return $fileInfos;
    }
}
