<?php declare(strict_types=1);

namespace Symplify\Statie\Generator;

use Symplify\Statie\Generator\Contract\ObjectSorterInterface;
use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;

final class FileNameObjectSorter implements ObjectSorterInterface
{
    /**
     * @param AbstractGeneratorFile[] $generatorFiles
     * @return AbstractGeneratorFile[]
     */
    public function sort(array $generatorFiles): array
    {
        usort($generatorFiles, function (AbstractGeneratorFile $firstFile, AbstractGeneratorFile $secondFile): int {
            // from newest to oldest, Z to A
            return strcmp($secondFile->getFilePath(), $firstFile->getFilePath());
        });

        return $generatorFiles;
    }
}
