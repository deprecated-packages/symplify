<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Contract;

use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;

interface ObjectSorterInterface
{
    /**
     * @param AbstractGeneratorFile[] $generatorFiles
     * @return AbstractGeneratorFile[]
     */
    public function sort(array $generatorFiles): array;
}
