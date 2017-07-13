<?php declare(strict_types=1);

namespace Symplify\Statie\Contract\Renderable;

use Symplify\Statie\Renderable\File\AbstractFile;

interface FileDecoratorInterface
{
    /**
     * @param AbstractFile[] $files
     * @return AbstractFile[]
     */
    public function decorateFiles(array $files): array;

    /**
     * Higher priorities are executed first.
     */
    public function getPriority(): int;
}
