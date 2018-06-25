<?php declare(strict_types=1);

namespace Symplify\Statie\Contract\Templating;

use Symplify\Statie\Renderable\File\AbstractFile;

interface RendererInterface
{
    /**
     * @param mixed[] $parameters
     */
    public function renderFileWithParameters(AbstractFile $file, array $parameters): string;
}
