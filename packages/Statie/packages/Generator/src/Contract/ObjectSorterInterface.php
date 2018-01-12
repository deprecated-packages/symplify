<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Contract;

use Symplify\Statie\Renderable\File\AbstractFile;

interface ObjectSorterInterface
{
    /**
     * @param AbstractFile[] $files
     * @return AbstractFile[]
     */
    public function sort(array $files): array;
}
