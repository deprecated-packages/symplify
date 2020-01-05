<?php

declare(strict_types=1);

namespace Symplify\Statie\Templating;

use Symplify\Statie\FileSystem\FileFinder;
use Twig\Loader\ArrayLoader;

final class LayoutsAndSnippetsLoader
{
    /**
     * @var bool
     */
    private $isLoaded = false;

    /**
     * @var FileFinder
     */
    private $fileFinder;

    /**
     * @var ArrayLoader
     */
    private $arrayLoader;

    public function __construct(FileFinder $fileFinder, ArrayLoader $arrayLoader)
    {
        $this->fileFinder = $fileFinder;
        $this->arrayLoader = $arrayLoader;
    }

    public function loadFromSource(string $source): void
    {
        if ($this->isLoaded) {
            return;
        }

        foreach ($this->fileFinder->findLayoutsAndSnippets($source) as $fileInfo) {
            $relativePathInSource = $fileInfo->getRelativeFilePathFromDirectory($source);

            if ($fileInfo->getExtension() === 'twig') {
                $this->arrayLoader->setTemplate($relativePathInSource, $fileInfo->getContents());
            }
        }

        $this->isLoaded = true;
    }
}
