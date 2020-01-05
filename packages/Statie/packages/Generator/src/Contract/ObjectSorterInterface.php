<?php

declare(strict_types=1);

namespace Symplify\Statie\Generator\Contract;

use Symplify\Statie\Renderable\File\AbstractFile;

interface ObjectSorterInterface
{
    /**
     * @param AbstractFile[] $generatorFiles
     * @return AbstractFile[]
     */
    public function sort(array $generatorFiles): array;
}
