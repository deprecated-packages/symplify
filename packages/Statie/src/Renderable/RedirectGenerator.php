<?php

declare(strict_types=1);

namespace Symplify\Statie\Renderable;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symplify\Statie\Renderable\File\VirtualFile;

final class RedirectGenerator
{
    /**
     * @var string
     */
    private const REDIRECT_TEMPLATE = __DIR__ . '/../../templates/redirect.html';

    /**
     * @var string[]
     */
    private $redirects = [];

    /**
     * @param string[] $redirects
     */
    public function __construct(array $redirects)
    {
        $this->redirects = $redirects;
    }

    /**
     * @return VirtualFile[]
     */
    public function generate(): array
    {
        $virtualFiles = [];

        foreach ($this->redirects as $oldPath => $newPath) {
            $outputPath = $this->createOutputPath($oldPath);
            $content = $this->createRedirectContent($newPath);

            $virtualFiles[] = new VirtualFile($outputPath, $content);
        }

        return $virtualFiles;
    }

    private function createOutputPath(string $path): string
    {
        $path = rtrim($path, '/') . '/';
        return $path . 'index.html';
    }

    private function createRedirectContent(string $newPath): string
    {
        $redirectContent = FileSystem::read(self::REDIRECT_TEMPLATE);

        $newPath = $this->normalizeNewPath($newPath);

        return str_replace('__URL__', $newPath, $redirectContent);
    }

    private function normalizeNewPath(string $newPath): string
    {
        if (Strings::startsWith($newPath, 'http')) {
            return $newPath;
        }

        // make path relative to root
        $newPath = '/' . ltrim($newPath, '/');
        return rtrim($newPath, '/') . '/';
    }
}
