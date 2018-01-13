<?php declare(strict_types=1);

namespace Symplify\Statie\Generator;

use Symplify\Statie\Generator\Contract\ObjectSorterInterface;
use Symplify\Statie\Renderable\File\AbstractFile;

final class FileNameObjectSorter implements ObjectSorterInterface
{
    /**
     * @param AbstractFile[] $files
     * @return AbstractFile[]
     */
    public function sort(array $files): array
    {
        usort($files, function (AbstractFile $firstFile, AbstractFile $seconFile): int {
            // from newest to oldest, Z to A
            return strcmp($seconFile->getFilePath(), $firstFile->getFilePath());
        });

        return $files;
    }
}
