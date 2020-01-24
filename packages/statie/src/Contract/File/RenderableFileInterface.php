<?php

declare(strict_types=1);

namespace Symplify\Statie\Contract\File;

interface RenderableFileInterface
{
    /**
     * Relative path to where should be file printed
     */
    public function getOutputPath(): string;

    /**
     * Full content of file to be printed
     */
    public function getContent(): string;
}
