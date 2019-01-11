<?php declare(strict_types=1);

namespace Symplify\Statie\Templating;

use Symplify\Statie\FileSystem\FileFinder;
use Symplify\Statie\Latte\Loader\ArrayLoader;
use Twig\Loader\ArrayLoader as TwigArrayLoader;

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
    private $latteArrayLoader;

    /**
     * @var TwigArrayLoader
     */
    private $twigArrayLoader;

    public function __construct(
        FileFinder $fileFinder,
        ArrayLoader $latteArrayLoader,
        TwigArrayLoader $twigArrayLoader
) {
        $this->fileFinder = $fileFinder;
        $this->latteArrayLoader = $latteArrayLoader;
        $this->twigArrayLoader = $twigArrayLoader;
    }

    public function loadFromSource(string $source): void
    {
        if ($this->isLoaded) {
            return;
        }

        foreach ($this->fileFinder->findLayoutsAndSnippets($source) as $fileInfo) {
            $relativePathInSource = $fileInfo->getRelativeFilePathFromDirectory($source);

            if ($fileInfo->getExtension() === 'twig') {
                $this->twigArrayLoader->setTemplate($relativePathInSource, $fileInfo->getContents());
            }

            if ($fileInfo->getExtension() === 'latte') {
                // before: "post"
                // now: "_layouts/post.latte"
                $this->latteArrayLoader->changeContent($relativePathInSource, $fileInfo->getContents());
            }
        }

        $this->isLoaded = true;
    }
}
