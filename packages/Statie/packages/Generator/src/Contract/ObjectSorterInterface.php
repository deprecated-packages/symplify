<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Contract;

use Symplify\Statie\Renderable\File\AbstractGeneratorFile;

interface ObjectSorterInterface
{
    /**
     * @param AbstractGeneratorFile[] $files
     * @return AbstractGeneratorFile[]
     */
    public function sort(array $files): array;
}
