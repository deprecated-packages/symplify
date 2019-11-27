<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\File;

use Symplify\Statie\Contract\File\RenderableFileInterface;

/**
 * This file doesn't exist, it will be only created and printed.
 * No source path, no file info.
 */
final class VirtualFile implements RenderableFileInterface
{
    /**
     * @var string
     */
    private $outputPath;

    /**
     * @var string
     */
    private $content;

    public function __construct(string $outputPath, string $content)
    {
        $this->outputPath = $outputPath;
        $this->content = $content;
    }

    public function getOutputPath(): string
    {
        return $this->outputPath;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
