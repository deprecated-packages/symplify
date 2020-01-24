<?php

declare(strict_types=1);

namespace Symplify\Statie\Generator;

use Symplify\Statie\Generator\Contract\ObjectSorterInterface;
use Symplify\Statie\Renderable\File\AbstractFile;

final class FileNameObjectSorter implements ObjectSorterInterface
{
    /**
     * @param AbstractFile[] $generatorFiles
     * @return AbstractFile[]
     */
    public function sort(array $generatorFiles): array
    {
        uasort($generatorFiles, function (AbstractFile $firstFile, AbstractFile $secondFile): int {
            // from newest to oldest, Z to A
            return strcmp($secondFile->getFilePath(), $firstFile->getFilePath());
        });

        return $generatorFiles;
    }
}
